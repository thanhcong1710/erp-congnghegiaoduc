import apiClient from './client';
import { Student, Contract, Payment, Assessment, Session } from '../types';

export const studentApi = {
    // Get student info
    getStudentInfo: async (studentId: number) => {
        return apiClient.get(`/lms/students/show/${studentId}`);
    },

    // Get student contracts
    getStudentContracts: async (studentId: number) => {
        return apiClient.post('/lms/students/contracts', {
            student_id: studentId,
        });
    },

    // Get student sessions (schedule)
    getStudentSessions: async (studentId: number, startDate?: string, endDate?: string) => {
        return apiClient.post('/lms/students/sessions', {
            student_id: studentId,
            start_date: startDate,
            end_date: endDate,
        });
    },

    // Get student assessments (grades)
    getStudentAssessments: async (studentId: number) => {
        return apiClient.post('/lms/students/assessments', {
            student_id: studentId,
        });
    },

    // Get student payments
    getStudentPayments: async (studentId: number) => {
        return apiClient.post('/lms/students/payments', {
            student_id: studentId,
        });
    },

    // Get student logs
    getStudentLogs: async (studentId: number) => {
        return apiClient.post('/lms/students/logs', {
            student_id: studentId,
        });
    },

    // Update student info
    updateStudentInfo: async (data: Partial<Student> & { id: number }) => {
        return apiClient.post('/lms/students/update', data);
    },

    // Upload student avatar
    uploadStudentAvatar: async (studentId: number, formData: FormData) => {
        return apiClient.post('/lms/students/upload-avatar', formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        });
    },

    // Get parent's students (for parent login)
    getParentStudents: async (parentId: number) => {
        return apiClient.get(`/crm/students/get_all_data/${parentId}`);
    },

    // Get notifications/tickets
    getNotifications: async (parentId: number) => {
        return apiClient.get(`/crm/ticket/get_all_data/${parentId}`);
    },
};
