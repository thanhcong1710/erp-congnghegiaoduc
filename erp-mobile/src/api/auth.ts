import apiClient from './client';
import { LoginRequest, LoginResponse, User } from '../types';

export const authApi = {
    // Login
    login: async (credentials: LoginRequest) => {
        return apiClient.post<LoginResponse>('/auth/login', credentials);
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
