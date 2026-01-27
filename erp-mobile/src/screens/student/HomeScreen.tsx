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
import { studentApi } from '../../api/student';
import { COLORS } from '../../constants/colors';

export default function StudentHomeScreen() {
    const { user } = useAppSelector((state) => state.auth);
    const [loading, setLoading] = useState(false);
    const [refreshing, setRefreshing] = useState(false);
    const [studentInfo, setStudentInfo] = useState<any>(null);
    const [contracts, setContracts] = useState<any[]>([]);
    const [upcomingSessions, setUpcomingSessions] = useState<any[]>([]);

    const loadData = async () => {
        if (!user) return;

        try {
            setLoading(true);

            // For parent login, get first student
            // For student login, use user.id
            const studentId = user.id;

            const [infoResponse, contractsResponse, sessionsResponse] = await Promise.all([
                studentApi.getStudentInfo(studentId),
                studentApi.getStudentContracts(studentId),
                studentApi.getStudentSessions(studentId),
            ]);

            setStudentInfo(infoResponse.data);
            setContracts(contractsResponse.data || []);
            setUpcomingSessions(sessionsResponse.data?.slice(0, 5) || []);
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
                        <Text style={styles.userName}>{user?.full_name || 'Học sinh'}</Text>
                    </View>
                    <TouchableOpacity style={styles.notificationButton}>
                        <Ionicons name="notifications-outline" size={24} color={COLORS.white} />
                        <View style={styles.badge}>
                            <Text style={styles.badgeText}>2</Text>
                        </View>
                    </TouchableOpacity>
                </View>

                {/* Stats Cards */}
                <View style={styles.statsContainer}>
                    <View style={[styles.statCard, { backgroundColor: COLORS.student.primary }]}>
                        <Ionicons name="book-outline" size={32} color={COLORS.white} />
                        <Text style={styles.statNumber}>{contracts.length}</Text>
                        <Text style={styles.statLabel}>Khóa học</Text>
                    </View>

                    <View style={[styles.statCard, { backgroundColor: COLORS.secondary }]}>
                        <Ionicons name="calendar-outline" size={32} color={COLORS.white} />
                        <Text style={styles.statNumber}>{upcomingSessions.length}</Text>
                        <Text style={styles.statLabel}>Buổi học sắp tới</Text>
                    </View>

                    <View style={[styles.statCard, { backgroundColor: COLORS.success }]}>
                        <Ionicons name="trophy-outline" size={32} color={COLORS.white} />
                        <Text style={styles.statNumber}>85%</Text>
                        <Text style={styles.statLabel}>Điểm TB</Text>
                    </View>
                </View>

                {/* Upcoming Sessions */}
                <View style={styles.section}>
                    <View style={styles.sectionHeader}>
                        <Text style={styles.sectionTitle}>Lịch học sắp tới</Text>
                        <TouchableOpacity>
                            <Text style={styles.seeAll}>Xem tất cả</Text>
                        </TouchableOpacity>
                    </View>

                    {loading ? (
                        <ActivityIndicator size="large" color={COLORS.student.primary} />
                    ) : upcomingSessions.length > 0 ? (
                        upcomingSessions.map((item, index) => (
                            <View key={index} style={styles.sessionCard}>
                                <View style={styles.sessionDate}>
                                    <Text style={styles.sessionDay}>
                                        {new Date(item.session_date).getDate()}
                                    </Text>
                                    <Text style={styles.sessionMonth}>
                                        Tháng {new Date(item.session_date).getMonth() + 1}
                                    </Text>
                                </View>

                                <View style={styles.sessionContent}>
                                    <Text style={styles.sessionTitle}>{item.class_name}</Text>
                                    <View style={styles.sessionInfo}>
                                        <Ionicons name="time-outline" size={14} color={COLORS.textSecondary} />
                                        <Text style={styles.sessionInfoText}>
                                            {item.session_start_time} - {item.session_end_time}
                                        </Text>
                                    </View>
                                    <View style={styles.sessionInfo}>
                                        <Ionicons name="location-outline" size={14} color={COLORS.textSecondary} />
                                        <Text style={styles.sessionInfoText}>
                                            {item.room_name || 'Phòng chưa xác định'}
                                        </Text>
                                    </View>
                                </View>
                            </View>
                        ))
                    ) : (
                        <View style={styles.emptyState}>
                            <Ionicons name="calendar-outline" size={64} color={COLORS.gray} />
                            <Text style={styles.emptyStateText}>
                                Không có lịch học sắp tới
                            </Text>
                        </View>
                    )}
                </View>

                {/* Quick Actions */}
                <View style={styles.section}>
                    <Text style={styles.sectionTitle}>Thao tác nhanh</Text>
                    <View style={styles.quickActions}>
                        <TouchableOpacity style={styles.actionButton}>
                            <View style={[styles.actionIcon, { backgroundColor: COLORS.student.light }]}>
                                <Ionicons name="calendar" size={24} color={COLORS.student.primary} />
                            </View>
                            <Text style={styles.actionText}>Lịch học</Text>
                        </TouchableOpacity>

                        <TouchableOpacity style={styles.actionButton}>
                            <View style={[styles.actionIcon, { backgroundColor: COLORS.secondaryLight }]}>
                                <Ionicons name="trophy" size={24} color={COLORS.secondary} />
                            </View>
                            <Text style={styles.actionText}>Điểm số</Text>
                        </TouchableOpacity>

                        <TouchableOpacity style={styles.actionButton}>
                            <View style={[styles.actionIcon, { backgroundColor: COLORS.primaryLight }]}>
                                <Ionicons name="card" size={24} color={COLORS.primary} />
                            </View>
                            <Text style={styles.actionText}>Học phí</Text>
                        </TouchableOpacity>

                        <TouchableOpacity style={styles.actionButton}>
                            <View style={[styles.actionIcon, { backgroundColor: '#E8F5E9' }]}>
                                <Ionicons name="notifications" size={24} color={COLORS.success} />
                            </View>
                            <Text style={styles.actionText}>Thông báo</Text>
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
        backgroundColor: COLORS.student.primary,
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
        color: COLORS.student.primary,
        fontWeight: '600',
    },
    sessionCard: {
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
    sessionDate: {
        width: 60,
        alignItems: 'center',
        justifyContent: 'center',
        marginRight: 16,
        backgroundColor: COLORS.student.light,
        borderRadius: 8,
    },
    sessionDay: {
        fontSize: 24,
        fontWeight: 'bold',
        color: COLORS.student.primary,
    },
    sessionMonth: {
        fontSize: 12,
        color: COLORS.student.dark,
    },
    sessionContent: {
        flex: 1,
    },
    sessionTitle: {
        fontSize: 16,
        fontWeight: 'bold',
        color: COLORS.textPrimary,
        marginBottom: 8,
    },
    sessionInfo: {
        flexDirection: 'row',
        alignItems: 'center',
        marginTop: 4,
    },
    sessionInfoText: {
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
