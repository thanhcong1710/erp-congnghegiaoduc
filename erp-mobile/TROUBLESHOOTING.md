# Troubleshooting Guide

## Common Issues and Solutions

### 1. Node Version Error

**Error:**
```
ReferenceError: ReadableStream is not defined
npm WARN EBADENGINE Unsupported engine
```

**Cause:** Node.js version 16.x is too old. React Native 0.81+ requires Node 18+.

**Solution:**

#### Option A: Upgrade Node.js (Recommended)
```bash
# Using nvm (Node Version Manager)
nvm install 20
nvm use 20

# Or using Homebrew on Mac
brew install node@20
brew link node@20
```

#### Option B: Use older Expo version
```bash
# Downgrade to Expo SDK 50 (supports Node 16)
npm install expo@~50.0.0
```

### 2. Cannot Run on Web

**Error:**
```
expo start --web fails
```

**Solution:**
Web platform có thể có issues với Node 16. Thay vào đó, chạy trên iOS/Android:

```bash
# iOS (requires Mac + Xcode)
npm run ios

# Android (requires Android Studio)
npm run android

# Or use Expo Go on physical device
npm start
# Then scan QR code with Expo Go app
```

### 3. Metro Bundler Issues

**Error:**
```
Metro bundler failed to start
```

**Solution:**
```bash
# Clear cache and restart
npm start -- --clear

# Or
expo start -c
```

### 4. iOS Build Errors

**Error:**
```
Pod install failed
```

**Solution:**
```bash
cd ios
pod deintegrate
pod install
cd ..
npm run ios
```

### 5. Android Build Errors

**Error:**
```
Gradle build failed
```

**Solution:**
```bash
cd android
./gradlew clean
cd ..
npm run android
```

### 6. API Connection Issues

**Error:**
```
Network request failed
API timeout
```

**Solution:**

1. **Check API URL in config:**
```typescript
// src/constants/config.ts
export const API_CONFIG = {
  BASE_URL: 'http://your-api-url.com/api',
  TIMEOUT: 30000,
};
```

2. **For iOS Simulator:**
   - Use `http://localhost:8000/api` if backend runs on same machine
   - Or use your machine's IP: `http://192.168.1.x:8000/api`

3. **For Android Emulator:**
   - Use `http://10.0.2.2:8000/api` (Android emulator's localhost)
   - Or use your machine's IP: `http://192.168.1.x:8000/api`

4. **For Physical Device:**
   - Must use your machine's IP address
   - Ensure device and computer are on same WiFi network
   - Example: `http://192.168.1.100:8000/api`

### 7. Authentication Issues

**Error:**
```
401 Unauthorized
Token expired
```

**Solution:**
```typescript
// Clear stored auth data
import AsyncStorage from '@react-native-async-storage/async-storage';

AsyncStorage.multiRemove([
  '@erp_token',
  '@erp_user',
  '@erp_user_type',
]);
```

### 8. TypeScript Errors

**Error:**
```
Type 'X' is not assignable to type 'Y'
```

**Solution:**
```bash
# Restart TypeScript server in VS Code
# Press Cmd+Shift+P (Mac) or Ctrl+Shift+P (Windows)
# Type: "TypeScript: Restart TS Server"

# Or rebuild
npm run tsc
```

### 9. Dependencies Issues

**Error:**
```
Module not found
Cannot find module 'X'
```

**Solution:**
```bash
# Reinstall dependencies
rm -rf node_modules
rm package-lock.json
npm install

# Or
npm ci
```

### 10. Expo Go Issues

**Error:**
```
Unable to connect to development server
```

**Solution:**

1. **Check same network:**
   - Ensure phone and computer on same WiFi
   - Disable VPN if active

2. **Manual connection:**
   - In Expo Go, tap "Enter URL manually"
   - Enter: `exp://YOUR_IP:8081`

3. **Tunnel mode:**
   ```bash
   expo start --tunnel
   ```

## Development Tips

### 1. Hot Reload Not Working

```bash
# Shake device or press:
# iOS: Cmd+D
# Android: Cmd+M (Mac) or Ctrl+M (Windows)
# Select "Enable Fast Refresh"
```

### 2. Debug Menu

**iOS Simulator:**
- Press `Cmd + D`

**Android Emulator:**
- Press `Cmd + M` (Mac) or `Ctrl + M` (Windows)

**Physical Device:**
- Shake the device

### 3. View Logs

```bash
# iOS
npx react-native log-ios

# Android
npx react-native log-android

# Or in Expo
# Logs appear in terminal where you ran `npm start`
```

### 4. Performance Issues

```bash
# Enable Hermes (if not already enabled)
# In app.json:
{
  "expo": {
    "jsEngine": "hermes"
  }
}
```

### 5. Clear All Caches

```bash
# Clear everything
rm -rf node_modules
rm -rf .expo
rm -rf ios/build
rm -rf android/build
rm package-lock.json
npm install
npm start -- --clear
```

## Platform-Specific Issues

### iOS

1. **Simulator not found:**
```bash
# List available simulators
xcrun simctl list devices

# Open simulator
open -a Simulator
```

2. **Code signing issues:**
   - Open `ios/YourApp.xcworkspace` in Xcode
   - Select your team in Signing & Capabilities

### Android

1. **Emulator not starting:**
```bash
# List available AVDs
emulator -list-avds

# Start specific AVD
emulator -avd Pixel_4_API_30
```

2. **ADB issues:**
```bash
# Restart ADB
adb kill-server
adb start-server
```

## Getting Help

### Check Logs
Always check the terminal output and device logs for detailed error messages.

### Common Log Locations
- Metro bundler: Terminal where `npm start` is running
- iOS: Xcode console or `npx react-native log-ios`
- Android: Android Studio Logcat or `npx react-native log-android`

### Resources
- [Expo Documentation](https://docs.expo.dev/)
- [React Native Documentation](https://reactnative.dev/)
- [Stack Overflow](https://stackoverflow.com/questions/tagged/react-native)

## Quick Reference

### Recommended Node Version
```bash
node --version
# Should be: v18.x or v20.x
```

### Recommended Development Setup
- **Node.js**: 18.x or 20.x
- **npm**: 8.x or higher
- **Expo CLI**: Latest
- **iOS**: Xcode 14+ (Mac only)
- **Android**: Android Studio with SDK 31+

### Environment Check
```bash
# Check versions
node --version
npm --version
expo --version

# Check Expo doctor
npx expo-doctor
```

## Contact Support

If issues persist:
1. Check PROJECT_STATUS.md for known issues
2. Review API_GUIDE.md for backend configuration
3. Consult with development team
