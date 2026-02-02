import apiClient from './client';
import { LoginRequest, LoginResponse, User } from '../types';

export const authApi = {
    // Login
    login: async (credentials: LoginRequest) => {
        // Map username to hrm_id as expected by backend
        const payload = {
            hrm_id: credentials.username,
            password: credentials.password
        };

        // Call API
        const response = await apiClient.post<any>('/auth/login', payload);

        // Transform backend response to LoginResponse format
        if (response && (response as any).status === 1) {
            const backendData = response as any;
            return {
                code: 200,
                message: 'Login successful',
                data: {
                    token: backendData.accessToken,
                    user: backendData.userData,
                    status: backendData.status,
                    accessToken: backendData.accessToken,
                    userData: backendData.userData
                }
            };
        } else if (response && (response as any).status === 0) {
            // Handle backend error (status 0)
            throw {
                response: {
                    data: {
                        message: (response as any).message || 'Login failed'
                    }
                }
            };
        }

        return response;
    },

    // Logout
    logout: async () => {
        return apiClient.post('/auth/logout');
    },

    // Get current user menu
    getMenu: async () => {
        return apiClient.get('/menu');
    },

    // Change password
    changePassword: async (data: {
        old_password: string;
        new_password: string;
        confirm_password: string;
    }) => {
        return apiClient.post('/user/change-password', data);
    },

    // Update user info
    updateInfo: async (data: Partial<User>) => {
        return apiClient.post('/user/update-info', data);
    },

    // Upload avatar
    uploadAvatar: async (formData: FormData) => {
        return apiClient.post('/user/upload-avatar', formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        });
    },
};
