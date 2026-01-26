import axios, { AxiosInstance, AxiosRequestConfig, AxiosResponse } from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { API_CONFIG, STORAGE_KEYS } from '../constants/config';
import { ApiResponse } from '../types';

class ApiClient {
    private instance: AxiosInstance;

    constructor() {
        this.instance = axios.create({
            baseURL: API_CONFIG.BASE_URL,
            timeout: API_CONFIG.TIMEOUT,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
        });

        this.setupInterceptors();
    }

    private setupInterceptors() {
        // Request interceptor
        this.instance.interceptors.request.use(
            async (config) => {
                const token = await AsyncStorage.getItem(STORAGE_KEYS.TOKEN);
                if (token) {
                    config.headers.Authorization = `Bearer ${token}`;
                }
                return config;
            },
            (error) => {
                return Promise.reject(error);
            }
        );

        // Response interceptor
        this.instance.interceptors.response.use(
            (response) => response,
            async (error) => {
                if (error.response?.status === 401) {
                    // Token expired or invalid
                    await AsyncStorage.multiRemove([
                        STORAGE_KEYS.TOKEN,
                        STORAGE_KEYS.USER,
                        STORAGE_KEYS.USER_TYPE,
                    ]);
                    // You can dispatch a logout action here if needed
                }
                return Promise.reject(error);
            }
        );
    }

    async get<T = any>(
        url: string,
        config?: AxiosRequestConfig
    ): Promise<ApiResponse<T>> {
        const response: AxiosResponse<ApiResponse<T>> = await this.instance.get(
            url,
            config
        );
        return response.data;
    }

    async post<T = any>(
        url: string,
        data?: any,
        config?: AxiosRequestConfig
    ): Promise<ApiResponse<T>> {
        const response: AxiosResponse<ApiResponse<T>> = await this.instance.post(
            url,
            data,
            config
        );
        return response.data;
    }

    async put<T = any>(
        url: string,
        data?: any,
        config?: AxiosRequestConfig
    ): Promise<ApiResponse<T>> {
        const response: AxiosResponse<ApiResponse<T>> = await this.instance.put(
            url,
            data,
            config
        );
        return response.data;
    }

    async delete<T = any>(
        url: string,
        config?: AxiosRequestConfig
    ): Promise<ApiResponse<T>> {
        const response: AxiosResponse<ApiResponse<T>> = await this.instance.delete(
            url,
            config
        );
        return response.data;
    }

    async patch<T = any>(
        url: string,
        data?: any,
        config?: AxiosRequestConfig
    ): Promise<ApiResponse<T>> {
        const response: AxiosResponse<ApiResponse<T>> = await this.instance.patch(
            url,
            data,
            config
        );
        return response.data;
    }
}

export default new ApiClient();
