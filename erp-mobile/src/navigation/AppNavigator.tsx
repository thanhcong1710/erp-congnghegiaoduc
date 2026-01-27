import React, { useEffect } from 'react';
import { NavigationContainer } from '@react-navigation/native';
import { createNativeStackNavigator } from '@react-navigation/native-stack';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
import { ActivityIndicator, View } from 'react-native';
import { Ionicons } from '@expo/vector-icons';

import { useAppDispatch, useAppSelector } from '../store/hooks';
import { loadStoredAuth } from '../store/slices/authSlice';
import { COLORS } from '../constants/colors';

// Auth Screens
import LoginScreen from '../screens/auth/LoginScreen';

// Teacher Screens
import TeacherHomeScreen from '../screens/teacher/HomeScreen';
import TeacherScheduleScreen from '../screens/teacher/ScheduleScreen';
import TeacherClassesScreen from '../screens/teacher/ClassesScreen';
import TeacherProfileScreen from '../screens/teacher/ProfileScreen';

// Student Screens
import StudentHomeScreen from '../screens/student/HomeScreen';
import StudentScheduleScreen from '../screens/student/ScheduleScreen';
import StudentGradesScreen from '../screens/student/GradesScreen';
import StudentPaymentsScreen from '../screens/student/PaymentsScreen';
import StudentProfileScreen from '../screens/student/ProfileScreen';

const Stack = createNativeStackNavigator();
const Tab = createBottomTabNavigator();

// Teacher Tab Navigator
function TeacherTabs() {
    return (
        <Tab.Navigator
            screenOptions={({ route }) => ({
                tabBarIcon: ({ focused, color, size }) => {
                    let iconName: keyof typeof Ionicons.glyphMap = 'home';

                    if (route.name === 'Home') {
                        iconName = focused ? 'home' : 'home-outline';
                    } else if (route.name === 'Schedule') {
                        iconName = focused ? 'calendar' : 'calendar-outline';
                    } else if (route.name === 'Classes') {
                        iconName = focused ? 'school' : 'school-outline';
                    } else if (route.name === 'Profile') {
                        iconName = focused ? 'person' : 'person-outline';
                    }

                    return <Ionicons name={iconName} size={size} color={color} />;
                },
                tabBarActiveTintColor: COLORS.teacher.primary,
                tabBarInactiveTintColor: COLORS.gray,
                headerShown: false,
            })}
        >
            <Tab.Screen
                name="Home"
                component={TeacherHomeScreen}
                options={{ title: 'Trang chủ', headerShown: false }}
            />
            <Tab.Screen
                name="Schedule"
                component={TeacherScheduleScreen}
                options={{ title: 'Lịch dạy', headerShown: false }}
            />
            <Tab.Screen
                name="Classes"
                component={TeacherClassesScreen}
                options={{ title: 'Lớp học', headerShown: false }}
            />
            <Tab.Screen
                name="Profile"
                component={TeacherProfileScreen}
                options={{ title: 'Cá nhân', headerShown: false }}
            />
        </Tab.Navigator>
    );
}

// Student Tab Navigator
function StudentTabs() {
    return (
        <Tab.Navigator
            screenOptions={({ route }) => ({
                tabBarIcon: ({ focused, color, size }) => {
                    let iconName: keyof typeof Ionicons.glyphMap = 'home';

                    if (route.name === 'Home') {
                        iconName = focused ? 'home' : 'home-outline';
                    } else if (route.name === 'Schedule') {
                        iconName = focused ? 'calendar' : 'calendar-outline';
                    } else if (route.name === 'Grades') {
                        iconName = focused ? 'trophy' : 'trophy-outline';
                    } else if (route.name === 'Payments') {
                        iconName = focused ? 'card' : 'card-outline';
                    } else if (route.name === 'Profile') {
                        iconName = focused ? 'person' : 'person-outline';
                    }

                    return <Ionicons name={iconName} size={size} color={color} />;
                },
                tabBarActiveTintColor: COLORS.student.primary,
                tabBarInactiveTintColor: COLORS.gray,
                headerShown: false,
            })}
        >
            <Tab.Screen
                name="Home"
                component={StudentHomeScreen}
                options={{ title: 'Trang chủ', headerShown: false }}
            />
            <Tab.Screen
                name="Schedule"
                component={StudentScheduleScreen}
                options={{ title: 'Lịch học', headerShown: false }}
            />
            <Tab.Screen
                name="Grades"
                component={StudentGradesScreen}
                options={{ title: 'Điểm số', headerShown: false }}
            />
            <Tab.Screen
                name="Payments"
                component={StudentPaymentsScreen}
                options={{ title: 'Học phí', headerShown: false }}
            />
            <Tab.Screen
                name="Profile"
                component={StudentProfileScreen}
                options={{ title: 'Cá nhân', headerShown: false }}
            />
        </Tab.Navigator>
    );
}

export default function AppNavigator() {
    const dispatch = useAppDispatch();
    const { isAuthenticated, isLoading, userType } = useAppSelector(
        (state) => state.auth
    );

    useEffect(() => {
        dispatch(loadStoredAuth());
    }, [dispatch]);

    if (isLoading) {
        return (
            <View style={{ flex: 1, justifyContent: 'center', alignItems: 'center' }}>
                <ActivityIndicator size="large" color={COLORS.primary} />
            </View>
        );
    }

    return (
        <NavigationContainer>
            <Stack.Navigator
                screenOptions={{
                    headerShown: false,
                    animation: 'fade',
                }}
            >
                {!isAuthenticated ? (
                    <Stack.Screen
                        name="Login"
                        component={LoginScreen}
                        options={{ headerShown: false }}
                    />
                ) : userType === 'teacher' ? (
                    <Stack.Screen
                        name="TeacherApp"
                        component={TeacherTabs}
                        options={{ headerShown: false }}
                    />
                ) : (
                    <Stack.Screen
                        name="StudentApp"
                        component={StudentTabs}
                        options={{ headerShown: false }}
                    />
                )}
            </Stack.Navigator>
        </NavigationContainer>
    );
}
