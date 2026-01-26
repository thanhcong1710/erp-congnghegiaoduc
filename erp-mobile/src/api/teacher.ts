import apiClient from './client';
import { Class, Session, Attendance, Schedule } from '../types';

export const teacherApi = {
    // Get teacher info
    getTeacherInfo: async (teacherId: number) => {
        return apiClient.get(`/lms/teachers/show/${teacherId}`);
    },

    // Get teacher's classes
    getTeacherClasses: async (teacherId: number, filters?: any) => {
        return apiClient.post('/settings/classes/load-classes', {
            teacher_id: teacherId,
            ...filters,
        });
    },

    // Get class sessions
    getClassSessions: async (classId: number) => {
        return apiClient.post('/settings/classes/sessions', {
            class_id: classId,
        });
    },

    // Get students in class
    getClassStudents: async (classId: number, sessionId?: number) => {
        return apiClient.post('/lms/attendances/load-students', {
            class_id: classId,
            session_id: sessionId,
        });
    },

    // Save attendance
    saveAttendance: async (data: {
        session_id: number;
        class_id: number;
        attendances: Array<{
            student_id: number;
            status: number;
            note?: string;
        }>;
    }) => {
        return apiClient.post('/lms/attendances/save', data);
    },

    // Get teacher schedule
    getTeacherSchedule: async (teacherId: number, startDate: string, endDate: string) => {
        return apiClient.post('/settings/classes/sessions', {
            teacher_id: teacherId,
            start_date: startDate,
            end_date: endDate,
        });
    },

    // Add assessment
    addAssessment: async (data: {
        student_id: number;
        class_id: number;
        assessment_type: string;
        score: number;
        max_score: number;
        assessment_date: string;
        note?: string;
    }) => {
        return apiClient.post('/lms/assessments/add', data);
    },

    // Get student assessments
    getStudentAssessments: async (studentId: number) => {
        return apiClient.post('/lms/students/assessments', {
            student_id: studentId,
        });
    },

    // Update teacher info
    updateTeacherInfo: async (teacherId: number, data: any) => {
        return apiClient.post(`/lms/teachers/update`, {
            id: teacherId,
            ...data,
        });
    },
};
