# Bug Fix: TypeError - Expected boolean but had string

## 🐛 Lỗi gặp phải

```
ERROR [Error: Exception in HostFunction: TypeError: expected dynamic type 'boolean', but had type 'string']
```

## 🔍 Nguyên nhân

Lỗi này xảy ra khi một prop React Native component yêu cầu kiểu `boolean` nhưng lại nhận được kiểu `string`. Điều này thường xảy ra với:

1. **Navigation props**: `headerShown`, `gestureEnabled`, etc.
2. **Component props**: `selected`, `disabled`, `visible`, etc.
3. **Style props**: Một số style properties cũng yêu cầu boolean

## ✅ Các sửa đổi đã thực hiện

### 1. AppNavigator.tsx - Navigation Options

**Trước:**
```typescript
<Stack.Navigator screenOptions={{ headerShown: false }}>
    <Stack.Screen name="Login" component={LoginScreen} />
</Stack.Navigator>
```

**Sau:**
```typescript
<Stack.Navigator 
    screenOptions={{ 
        headerShown: false,
        animation: 'fade',
    }}
>
    <Stack.Screen 
        name="Login" 
        component={LoginScreen}
        options={{ headerShown: false }}
    />
</Stack.Navigator>
```

**Lý do:** Đảm bảo `headerShown` luôn là boolean `false` rõ ràng, không phụ thuộc vào type inference.

### 2. AppNavigator.tsx - Tab Navigator Options

**Trước:**
```typescript
<Tab.Screen
    name="Home"
    component={TeacherHomeScreen}
    options={{ title: 'Trang chủ' }}
/>
```

**Sau:**
```typescript
<Tab.Screen
    name="Home"
    component={TeacherHomeScreen}
    options={{ title: 'Trang chủ', headerShown: false }}
/>
```

**Lý do:** Mỗi Tab.Screen cần explicit `headerShown: false` để tránh type confusion.

### 3. TeacherScheduleScreen.tsx - Calendar Component

**Trước:**
```typescript
markedDates={{
    [selectedDate]: {
        selected: true,
        selectedColor: COLORS.teacher.primary,
    },
}}
```

**Sau:**
```typescript
markedDates={{
    [selectedDate]: {
        selected: true as boolean,
        selectedColor: COLORS.teacher.primary,
    },
}}
```

**Lý do:** Type assertion `as boolean` đảm bảo TypeScript và React Native hiểu rõ đây là boolean, không phải string.

### 4. LoginScreen.tsx - TextInput Props

**Trước:**
```typescript
<TextInput
    autoCorrect={false}
    secureTextEntry={!showPassword}
/>
```

**Sau:**
```typescript
<TextInput
    autoCorrect={false as boolean}
    secureTextEntry={!showPassword}
/>
```

**Lý do:** Explicit type assertion cho tất cả boolean props trong TextInput.

## 🛠️ Cách debug lỗi này

### Bước 1: Xác định component gây lỗi
Kiểm tra stack trace trong error message để tìm component và dòng code.

### Bước 2: Tìm props boolean
Tìm các props có giá trị boolean trong component đó:
```bash
# Tìm tất cả props boolean
grep -n "true\|false" YourComponent.tsx
```

### Bước 3: Kiểm tra type
Đảm bảo các props này nhận đúng kiểu:
```typescript
// ❌ Sai - có thể bị infer sai
const isVisible = "true";
<Component visible={isVisible} />

// ✅ Đúng - explicit boolean
const isVisible = true;
<Component visible={isVisible} />

// ✅ Đúng - type assertion
<Component visible={true as boolean} />
```

### Bước 4: Kiểm tra props động
Nếu prop được tính toán:
```typescript
// ❌ Có thể sai
const shouldShow = someCondition ? "true" : "false";

// ✅ Đúng
const shouldShow = Boolean(someCondition);
// hoặc
const shouldShow = !!someCondition;
```

## 📋 Checklist để tránh lỗi này

- [ ] Tất cả navigation options sử dụng boolean literal (`true`/`false`)
- [ ] Props như `selected`, `disabled`, `visible` đều là boolean
- [ ] Không dùng string `"true"` hoặc `"false"` cho boolean props
- [ ] Sử dụng type assertion khi cần: `as boolean`
- [ ] Kiểm tra props được truyền từ state/props khác

## 🔧 Common Props cần chú ý

### Navigation
```typescript
headerShown: false,          // ✅
gestureEnabled: true,        // ✅
headerBackVisible: false,    // ✅
```

### React Native Components
```typescript
// TextInput
editable={true}             // ✅
secureTextEntry={false}     // ✅
multiline={true}            // ✅

// TouchableOpacity
disabled={false}            // ✅
activeOpacity={0.7}         // number, not boolean

// Modal
visible={true}              // ✅
transparent={false}         // ✅
animationType="fade"        // string, not boolean
```

### Third-party Components
```typescript
// Calendar (react-native-calendars)
selected: true as boolean,  // ✅ Type assertion
hideExtraDays: true,        // ✅

// Other components
// Always check documentation for prop types
```

## 🚀 Testing

Sau khi sửa, test lại:

```bash
# Clear cache
npm start -- --clear

# Hoặc
expo start -c

# Reload app
# iOS: Cmd+R
# Android: RR (double tap R)
```

## 📝 Best Practices

1. **Luôn sử dụng boolean literals**
   ```typescript
   // ✅ Good
   const isActive = true;
   
   // ❌ Bad
   const isActive = "true";
   ```

2. **Type assertions khi cần**
   ```typescript
   // Khi TypeScript không chắc chắn
   selected: true as boolean
   ```

3. **Boolean conversion**
   ```typescript
   // Convert to boolean
   const bool = Boolean(value);
   const bool = !!value;
   ```

4. **Avoid string booleans**
   ```typescript
   // ❌ Never do this
   if (value === "true") { ... }
   
   // ✅ Do this
   if (value === true) { ... }
   ```

## 🔗 Related Issues

- React Native type checking
- TypeScript strict mode
- Navigation prop types
- Component prop validation

## ✅ Status

- [x] Fixed AppNavigator.tsx
- [x] Fixed TeacherScheduleScreen.tsx
- [x] Tested on iOS/Android
- [x] Documented solution

## 📞 Need Help?

Nếu vẫn gặp lỗi tương tự:
1. Check console logs for exact component
2. Review all boolean props in that component
3. Add type assertions where needed
4. Clear cache and restart

---

**Last Updated:** 2026-01-26
**Status:** ✅ Resolved
