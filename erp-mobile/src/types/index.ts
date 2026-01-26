// User Types
export interface User {
    id: number;
    username: string;
    email: string;
    full_name: string;
    avatar?: string;
    role_id: number;
    branch_id?: number;
    phone?: string;
}

export interface Teacher extends User {
    subjects?: Subject[];
    classes?: Class[];
}

export interface Student {
    id: number;
    name: string;
    avatar?: string;
    student_code?: string;
    date_of_birth?: string;
    gender?: number;
    phone?: string;
    email?: string;
    parent_id?: number;
    branch_id?: number;
}

export interface Parent extends User {
    students?: Student[];
}

// Class Types
export interface Class {
    id: number;
    cls_name: string;
    cls_code?: string;
    product_id: number;
    program_id: number;
    branch_id: number;
    teacher_id?: number;
    room_id?: number;
    shift_id?: number;
    cls_startdate?: string;
    cls_enddate?: string;
    cls_iscancelled?: number;
    max_students?: number;
    total_sessions?: number;
    teacher_name?: string;
    room_name?: string;
    shift_name?: string;
    product_name?: string;
    program_name?: string;
}

// Session Types
export interface Session {
    id: number;
    class_id: number;
    session_no: number;
    session_date: string;
    session_start_time?: string;
    session_end_time?: string;
    teacher_id?: number;
    room_id?: number;
    status?: number;
    note?: string;
}

// Attendance Types
export interface Attendance {
    id: number;
    session_id: number;
    student_id: number;
    status: number; // 0: absent, 1: present, 2: late, 3: excused
    note?: string;
    created_at?: string;
    student_name?: string;
}

export interface AttendanceStatus {
    value: number;
    label: string;
    color: string;
}

// Assessment Types
export interface Assessment {
    id: number;
    student_id: number;
    class_id: number;
    assessment_type: string;
    score: number;
    max_score: number;
    assessment_date: string;
    note?: string;
    created_by?: number;
    student_name?: string;
    class_name?: string;
}

// Contract Types
export interface Contract {
    id: number;
    student_id: number;
    contract_code: string;
    product_id: number;
    program_id?: number;
    tuition_fee_id?: number;
    total_charged: number;
    must_charge: number;
    debt_amount: number;
    discount_value?: number;
    start_date?: string;
    end_date?: string;
    status: number;
    enrolment_start_date?: string;
    enrolment_last_date?: string;
    product_name?: string;
    program_name?: string;
}

// Payment Types
export interface Payment {
    id: number;
    contract_id: number;
    amount: number;
    payment_date: string;
    payment_method?: string;
    note?: string;
    created_by?: number;
}

// Schedule Types
export interface Schedule {
    id: number;
    class_id: number;
    class_name: string;
    session_date: string;
    session_start_time: string;
    session_end_time: string;
    room_name?: string;
    teacher_name?: string;
    session_no?: number;
    total_students?: number;
}

// Subject Types
export interface Subject {
    id: number;
    name: string;
    code?: string;
}

// Notification Types
export interface Notification {
    id: number;
    title: string;
    message: string;
    type: string;
    created_at: string;
    is_read: boolean;
    user_id: number;
}

// API Response Types
export interface ApiResponse<T = any> {
    code: number;
    message: string;
    data?: T;
}

export interface PaginatedResponse<T> {
    data: T[];
    total: number;
    current_page: number;
    per_page: number;
    last_page: number;
}

// Auth Types
export interface LoginRequest {
    username: string;
    password: string;
}

export interface LoginResponse {
    token: string;
    user: User;
}

export interface AuthState {
    user: User | null;
    token: string | null;
    isAuthenticated: boolean;
    isLoading: boolean;
    error: string | null;
    userType: 'teacher' | 'student' | 'parent' | null;
}
