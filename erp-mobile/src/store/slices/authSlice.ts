import { createSlice, createAsyncThunk, PayloadAction } from '@reduxjs/toolkit';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { authApi } from '../../api/auth';
import { STORAGE_KEYS } from '../../constants/config';
import { AuthState, LoginRequest, User } from '../../types';

const initialState: AuthState = {
    user: null,
    token: null,
    isAuthenticated: false,
    isLoading: false,
    error: null,
    userType: null,
};

// Async thunks
export const login = createAsyncThunk(
    'auth/login',
    async (credentials: LoginRequest, { rejectWithValue }) => {
        try {
            const response = await authApi.login(credentials);
            if (response.code === 200 && response.data) {
                const { token, user } = response.data;

                // Save to AsyncStorage
                await AsyncStorage.setItem(STORAGE_KEYS.TOKEN, token);
                await AsyncStorage.setItem(STORAGE_KEYS.USER, JSON.stringify(user));

                // Determine user type based on role_id
                let userType: 'teacher' | 'student' | 'parent' = 'student';
                if (user.role_id === 4) { // Teacher role
                    userType = 'teacher';
                } else if (user.role_id === 68 || user.role_id === 69) { // Parent/Student roles
                    userType = 'parent';
                }

                await AsyncStorage.setItem(STORAGE_KEYS.USER_TYPE, userType);

                return { token, user, userType };
            }
            return rejectWithValue(response.message || 'Login failed');
        } catch (error: any) {
            return rejectWithValue(error.response?.data?.message || 'Network error');
        }
    }
);

export const logout = createAsyncThunk(
    'auth/logout',
    async (_, { rejectWithValue }) => {
        try {
            await authApi.logout();
            await AsyncStorage.multiRemove([
                STORAGE_KEYS.TOKEN,
                STORAGE_KEYS.USER,
                STORAGE_KEYS.USER_TYPE,
            ]);
            return null;
        } catch (error: any) {
            // Clear storage even if API call fails
            await AsyncStorage.multiRemove([
                STORAGE_KEYS.TOKEN,
                STORAGE_KEYS.USER,
                STORAGE_KEYS.USER_TYPE,
            ]);
            return null;
        }
    }
);

export const loadStoredAuth = createAsyncThunk(
    'auth/loadStored',
    async (_, { rejectWithValue }) => {
        try {
            const token = await AsyncStorage.getItem(STORAGE_KEYS.TOKEN);
            const userStr = await AsyncStorage.getItem(STORAGE_KEYS.USER);
            const userType = await AsyncStorage.getItem(STORAGE_KEYS.USER_TYPE);

            if (token && userStr) {
                const user = JSON.parse(userStr);
                return {
                    token,
                    user,
                    userType: userType as 'teacher' | 'student' | 'parent'
                };
            }
            return rejectWithValue('No stored auth');
        } catch (error) {
            return rejectWithValue('Failed to load stored auth');
        }
    }
);

const authSlice = createSlice({
    name: 'auth',
    initialState,
    reducers: {
        clearError: (state) => {
            state.error = null;
        },
        updateUser: (state, action: PayloadAction<User>) => {
            state.user = action.payload;
        },
    },
    extraReducers: (builder) => {
        // Login
        builder.addCase(login.pending, (state) => {
            state.isLoading = true;
            state.error = null;
        });
        builder.addCase(login.fulfilled, (state, action) => {
            state.isLoading = false;
            state.isAuthenticated = true;
            state.token = action.payload.token;
            state.user = action.payload.user;
            state.userType = action.payload.userType;
            state.error = null;
        });
        builder.addCase(login.rejected, (state, action) => {
            state.isLoading = false;
            state.isAuthenticated = false;
            state.error = action.payload as string;
        });

        // Logout
        builder.addCase(logout.fulfilled, (state) => {
            return initialState;
        });

        // Load stored auth
        builder.addCase(loadStoredAuth.pending, (state) => {
            state.isLoading = true;
        });
        builder.addCase(loadStoredAuth.fulfilled, (state, action) => {
            state.isLoading = false;
            state.isAuthenticated = true;
            state.token = action.payload.token;
            state.user = action.payload.user;
            state.userType = action.payload.userType;
        });
        builder.addCase(loadStoredAuth.rejected, (state) => {
            state.isLoading = false;
            state.isAuthenticated = false;
        });
    },
});

export const { clearError, updateUser } = authSlice.actions;
export default authSlice.reducer;
