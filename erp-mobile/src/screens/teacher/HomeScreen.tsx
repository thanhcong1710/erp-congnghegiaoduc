import React, { useEffect, useState } from 'react';
import {
    View,
    Text,
    StyleSheet,
    ScrollView,
    TouchableOpacity,
    RefreshControl,
    ActivityIndicator,
} from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { Ionicons } from '@expo/vector-icons';
import { useAppSelector } from '../../store/hooks';
import { teacherApi } from '../../api/teacher';
import { COLORS } from '../../constants/colors';

export default function TeacherHomeScreen() {
    const { user } = useAppSelector((state) => state.auth);
    const [loading, setLoading] = useState(false);
    const [refreshing, setRefreshing] = useState(false);
    const [todayClasses, setTodayClasses] = useState<any[]>([]);
    const [stats, setStats] = useState({
        totalClasses: 0,
        todayClasses: 0,
        totalStudents: 0,
    });

    const loadData = async () => {
        if (!user) return;

        try {
            setLoading(true);
            const today = new Date().toISOString().split('T')[0];

            // Load teacher's classes
            const classesResponse = await teacherApi.getTeacherClasses(user.id);
            const classes = classesResponse.data || [];

            // Load today's schedule
            const scheduleResponse = await teacherApi.getTeacherSchedule(
                user.id,
                today,
                today
            );
            const schedule = scheduleResponse.data || [];

            setTodayClasses(schedule);
            setStats({
                totalClasses: classes.length,
                todayClasses: schedule.length,
                totalStudents: classes.reduce((sum: number, cls: any) =>
                    sum + (cls.total_students || 0), 0
                ),
            });
        } catch (error) {
            console.error('Error loading data:', error);
        } finally {
            setLoading(false);
            setRefreshing(false);
        }
    };

    useEffect(() => {
        loadData();
    }, []);

    const onRefresh = () => {
        setRefreshing(true);
        loadData();
    };

    const getGreeting = () => {
        const hour = new Date().getHours();
        if (hour < 12) return 'Chào buổi sáng';
        if (hour < 18) return 'Chào buổi chiều';
        return 'Chào buổi tối';
    };

    return (
        <SafeAreaView style={styles.container}>
            <ScrollView
                refreshControl={
                    <RefreshControl refreshing={Boolean(refreshing)} onRefresh={onRefresh} />
                }
            >
                {/* Header */}
                <View style={styles.header}>
                    <View>
                        <Text style={styles.greeting}>{getGreeting()},</Text>
                        <Text style={styles.userName}>{user?.full_name || 'Giáo viên'}</Text>
                    </View>
                    <TouchableOpacity style={styles.notificationButton}>
                        <Ionicons name="notifications-outline" size={24} color={COLORS.white} />
                        <View style={styles.badge}>
                            <Text style={styles.badgeText}>3</Text>
                        </View>
                    </TouchableOpacity>
                </View>

                {/* Stats Cards */}
                <View style={styles.statsContainer}>
                    <View style={[styles.statCard, { backgroundColor: COLORS.teacher.primary }]}>
                        <Ionicons name="school-outline" size={32} color={COLORS.white} />
                        <Text style={styles.statNumber}>{stats.totalClasses}</Text>
                        <Text style={styles.statLabel}>Lớp đang dạy</Text>
                    </View>

                    <View style={[styles.statCard, { backgroundColor: COLORS.secondary }]}>
                        <Ionicons name="calendar-outline" size={32} color={COLORS.white} />
                        <Text style={styles.statNumber}>{stats.todayClasses}</Text>
                        <Text style={styles.statLabel}>Lớp hôm nay</Text>
                    </View>

                    <View style={[styles.statCard, { backgroundColor: COLORS.success }]}>
                        <Ionicons name="people-outline" size={32} color={COLORS.white} />
                        <Text style={styles.statNumber}>{stats.totalStudents}</Text>
                        <Text style={styles.statLabel}>Học sinh</Text>
                    </View>
                </View>

                {/* Today's Classes */}
                <View style={styles.section}>
                    <View style={styles.sectionHeader}>
                        <Text style={styles.sectionTitle}>Lịch dạy hôm nay</Text>
                        <TouchableOpacity>
                            <Text style={styles.seeAll}>Xem tất cả</Text>
                        </TouchableOpacity>
                    </View>

                    {loading ? (
                        <ActivityIndicator size="large" color={COLORS.teacher.primary} />
                    ) : todayClasses.length > 0 ? (
                        todayClasses.map((item, index) => (
                            <TouchableOpacity key={index} style={styles.classCard}>
                                <View style={styles.classCardLeft}>
                                    <View style={styles.timeContainer}>
                                        <Text style={styles.timeText}>
                                            {item.session_start_time || '08:00'}
                                        </Text>
                                        <Text style={styles.timeDivider}>-</Text>
                                        <Text style={styles.timeText}>
                                            {item.session_end_time || '10:00'}
                                        </Text>
                                    </View>
                                </View>

                                <View style={styles.classCardRight}>
                                    <Text style={styles.className}>{item.class_name}</Text>
                                    <View style={styles.classInfo}>
                                        <Ionicons name="location-outline" size={14} color={COLORS.textSecondary} />
                                        <Text style={styles.classInfoText}>
                                            {item.room_name || 'Phòng chưa xác định'}
                                        </Text>
                                    </View>
                                    <View style={styles.classInfo}>
                                        <Ionicons name="people-outline" size={14} color={COLORS.textSecondary} />
                                        <Text style={styles.classInfoText}>
                                            {item.total_students || 0} học sinh
                                        </Text>
                                    </View>
                                </View>
                            </TouchableOpacity>
                        ))
                    ) : (
                        <View style={styles.emptyState}>
                            <Ionicons name="calendar-outline" size={64} color={COLORS.gray} />
                            <Text style={styles.emptyStateText}>
                                Không có lớp học nào hôm nay
                            </Text>
                        </View>
                    )}
                </View>

                {/* Quick Actions */}
                <View style={styles.section}>
                    <Text style={styles.sectionTitle}>Thao tác nhanh</Text>
                    <View style={styles.quickActions}>
                        <TouchableOpacity style={styles.actionButton}>
                            <View style={[styles.actionIcon, { backgroundColor: COLORS.teacher.light }]}>
                                <Ionicons name="checkmark-done-outline" size={24} color={COLORS.teacher.primary} />
                            </View>
                            <Text style={styles.actionText}>Điểm danh</Text>
                        </TouchableOpacity>

                        <TouchableOpacity style={styles.actionButton}>
                            <View style={[styles.actionIcon, { backgroundColor: COLORS.secondaryLight }]}>
                                <Ionicons name="create-outline" size={24} color={COLORS.secondary} />
                            </View>
                            <Text style={styles.actionText}>Nhập điểm</Text>
                        </TouchableOpacity>

                        <TouchableOpacity style={styles.actionButton}>
                            <View style={[styles.actionIcon, { backgroundColor: COLORS.primaryLight }]}>
                                <Ionicons name="document-text-outline" size={24} color={COLORS.primary} />
                            </View>
                            <Text style={styles.actionText}>Báo cáo</Text>
                        </TouchableOpacity>

                        <TouchableOpacity style={styles.actionButton}>
                            <View style={[styles.actionIcon, { backgroundColor: '#E8F5E9' }]}>
                                <Ionicons name="chatbubbles-outline" size={24} color={COLORS.success} />
                            </View>
                            <Text style={styles.actionText}>Tin nhắn</Text>
                        </TouchableOpacity>
                    </View>
                </View>
            </ScrollView>
        </SafeAreaView>
    );
}

const styles = StyleSheet.create({
    container: {
        flex: 1,
        backgroundColor: COLORS.background,
    },
    header: {
        backgroundColor: COLORS.teacher.primary,
        padding: 20,
        paddingTop: 10,
        flexDirection: 'row',
        justifyContent: 'space-between',
        alignItems: 'center',
        borderBottomLeftRadius: 24,
        borderBottomRightRadius: 24,
    },
    greeting: {
        fontSize: 16,
        color: COLORS.white,
        opacity: 0.9,
    },
    userName: {
        fontSize: 24,
        fontWeight: 'bold',
        color: COLORS.white,
        marginTop: 4,
    },
    notificationButton: {
        position: 'relative',
    },
    badge: {
        position: 'absolute',
        top: -4,
        right: -4,
        backgroundColor: COLORS.error,
        borderRadius: 10,
        width: 20,
        height: 20,
        justifyContent: 'center',
        alignItems: 'center',
    },
    badgeText: {
        color: COLORS.white,
        fontSize: 10,
        fontWeight: 'bold',
    },
    statsContainer: {
        flexDirection: 'row',
        padding: 16,
        gap: 12,
    },
    statCard: {
        flex: 1,
        padding: 16,
        borderRadius: 16,
        alignItems: 'center',
        shadowColor: '#000',
        shadowOffset: { width: 0, height: 2 },
        shadowOpacity: 0.1,
        shadowRadius: 4,
        elevation: 3,
    },
    statNumber: {
        fontSize: 24,
        fontWeight: 'bold',
        color: COLORS.white,
        marginTop: 8,
    },
    statLabel: {
        fontSize: 12,
        color: COLORS.white,
        marginTop: 4,
        textAlign: 'center',
    },
    section: {
        padding: 16,
    },
    sectionHeader: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        alignItems: 'center',
        marginBottom: 16,
    },
    sectionTitle: {
        fontSize: 18,
        fontWeight: 'bold',
        color: COLORS.textPrimary,
    },
    seeAll: {
        fontSize: 14,
        color: COLORS.teacher.primary,
        fontWeight: '600',
    },
    classCard: {
        backgroundColor: COLORS.white,
        borderRadius: 12,
        padding: 16,
        marginBottom: 12,
        flexDirection: 'row',
        shadowColor: '#000',
        shadowOffset: { width: 0, height: 1 },
        shadowOpacity: 0.1,
        shadowRadius: 2,
        elevation: 2,
    },
    classCardLeft: {
        marginRight: 16,
        justifyContent: 'center',
    },
    timeContainer: {
        alignItems: 'center',
    },
    timeText: {
        fontSize: 14,
        fontWeight: '600',
        color: COLORS.teacher.primary,
    },
    timeDivider: {
        fontSize: 12,
        color: COLORS.gray,
        marginVertical: 2,
    },
    classCardRight: {
        flex: 1,
    },
    className: {
        fontSize: 16,
        fontWeight: 'bold',
        color: COLORS.textPrimary,
        marginBottom: 8,
    },
    classInfo: {
        flexDirection: 'row',
        alignItems: 'center',
        marginTop: 4,
    },
    classInfoText: {
        fontSize: 14,
        color: COLORS.textSecondary,
        marginLeft: 4,
    },
    emptyState: {
        alignItems: 'center',
        paddingVertical: 40,
    },
    emptyStateText: {
        fontSize: 16,
        color: COLORS.gray,
        marginTop: 16,
    },
    quickActions: {
        flexDirection: 'row',
        flexWrap: 'wrap',
        gap: 12,
        marginTop: 8,
    },
    actionButton: {
        width: '22%',
        alignItems: 'center',
    },
    actionIcon: {
        width: 56,
        height: 56,
        borderRadius: 28,
        justifyContent: 'center',
        alignItems: 'center',
        marginBottom: 8,
    },
    actionText: {
        fontSize: 12,
        color: COLORS.textPrimary,
        textAlign: 'center',
    },
});
