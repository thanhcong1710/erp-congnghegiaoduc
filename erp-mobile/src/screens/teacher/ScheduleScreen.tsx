import React, { useEffect, useState } from 'react';
import {
    View,
    Text,
    StyleSheet,
    ScrollView,
    TouchableOpacity,
    RefreshControl,
} from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { Calendar } from 'react-native-calendars';
import { Ionicons } from '@expo/vector-icons';
import { useAppSelector } from '../../store/hooks';
import { teacherApi } from '../../api/teacher';
import { COLORS } from '../../constants/colors';

export default function TeacherScheduleScreen() {
    const { user } = useAppSelector((state) => state.auth);
    const [selectedDate, setSelectedDate] = useState(
        new Date().toISOString().split('T')[0]
    );
    const [schedule, setSchedule] = useState<any[]>([]);
    const [refreshing, setRefreshing] = useState(false);
    const [markedDates, setMarkedDates] = useState<any>({});

    const loadSchedule = async (date: string) => {
        if (!user) return;

        try {
            const response = await teacherApi.getTeacherSchedule(user.id, date, date);
            setSchedule(response.data || []);
        } catch (error) {
            console.error('Error loading schedule:', error);
        } finally {
            setRefreshing(false);
        }
    };

    useEffect(() => {
        loadSchedule(selectedDate);
    }, [selectedDate]);

    const onRefresh = () => {
        setRefreshing(true);
        loadSchedule(selectedDate);
    };

    return (
        <SafeAreaView style={styles.container}>
            <View style={styles.header}>
                <Text style={styles.headerTitle}>Lịch dạy</Text>
            </View>

            <ScrollView
                refreshControl={
                    <RefreshControl refreshing={refreshing} onRefresh={onRefresh} />
                }
            >
                <Calendar
                    current={selectedDate}
                    onDayPress={(day) => setSelectedDate(day.dateString)}
                    markedDates={{
                        [selectedDate]: {
                            selected: true,
                            selectedColor: COLORS.teacher.primary,
                        },
                    }}
                    theme={{
                        todayTextColor: COLORS.teacher.primary,
                        selectedDayBackgroundColor: COLORS.teacher.primary,
                        arrowColor: COLORS.teacher.primary,
                    }}
                />

                <View style={styles.scheduleContainer}>
                    <Text style={styles.scheduleTitle}>
                        Lịch ngày {new Date(selectedDate).toLocaleDateString('vi-VN')}
                    </Text>

                    {schedule.length > 0 ? (
                        schedule.map((item, index) => (
                            <TouchableOpacity key={index} style={styles.scheduleCard}>
                                <View style={styles.timeIndicator}>
                                    <Text style={styles.timeText}>
                                        {item.session_start_time || '08:00'}
                                    </Text>
                                    <View style={styles.timeLine} />
                                    <Text style={styles.timeText}>
                                        {item.session_end_time || '10:00'}
                                    </Text>
                                </View>

                                <View style={styles.scheduleContent}>
                                    <Text style={styles.scheduleClassName}>{item.class_name}</Text>
                                    <View style={styles.scheduleInfo}>
                                        <Ionicons name="location" size={16} color={COLORS.textSecondary} />
                                        <Text style={styles.scheduleInfoText}>
                                            {item.room_name || 'Phòng chưa xác định'}
                                        </Text>
                                    </View>
                                    <View style={styles.scheduleInfo}>
                                        <Ionicons name="people" size={16} color={COLORS.textSecondary} />
                                        <Text style={styles.scheduleInfoText}>
                                            {item.total_students || 0} học sinh
                                        </Text>
                                    </View>
                                </View>

                                <TouchableOpacity style={styles.attendanceButton}>
                                    <Ionicons name="checkmark-circle" size={24} color={COLORS.teacher.primary} />
                                </TouchableOpacity>
                            </TouchableOpacity>
                        ))
                    ) : (
                        <View style={styles.emptyState}>
                            <Ionicons name="calendar-outline" size={64} color={COLORS.gray} />
                            <Text style={styles.emptyText}>Không có lịch dạy</Text>
                        </View>
                    )}
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
        backgroundColor: COLORS.white,
        padding: 16,
        borderBottomWidth: 1,
        borderBottomColor: COLORS.border,
    },
    headerTitle: {
        fontSize: 20,
        fontWeight: 'bold',
        color: COLORS.textPrimary,
    },
    scheduleContainer: {
        padding: 16,
    },
    scheduleTitle: {
        fontSize: 18,
        fontWeight: 'bold',
        color: COLORS.textPrimary,
        marginBottom: 16,
    },
    scheduleCard: {
        backgroundColor: COLORS.white,
        borderRadius: 12,
        padding: 16,
        marginBottom: 12,
        flexDirection: 'row',
        alignItems: 'center',
        shadowColor: '#000',
        shadowOffset: { width: 0, height: 1 },
        shadowOpacity: 0.1,
        shadowRadius: 2,
        elevation: 2,
    },
    timeIndicator: {
        alignItems: 'center',
        marginRight: 16,
    },
    timeText: {
        fontSize: 14,
        fontWeight: '600',
        color: COLORS.teacher.primary,
    },
    timeLine: {
        width: 2,
        height: 20,
        backgroundColor: COLORS.teacher.light,
        marginVertical: 4,
    },
    scheduleContent: {
        flex: 1,
    },
    scheduleClassName: {
        fontSize: 16,
        fontWeight: 'bold',
        color: COLORS.textPrimary,
        marginBottom: 8,
    },
    scheduleInfo: {
        flexDirection: 'row',
        alignItems: 'center',
        marginTop: 4,
    },
    scheduleInfoText: {
        fontSize: 14,
        color: COLORS.textSecondary,
        marginLeft: 4,
    },
    attendanceButton: {
        padding: 8,
    },
    emptyState: {
        alignItems: 'center',
        paddingVertical: 40,
    },
    emptyText: {
        fontSize: 16,
        color: COLORS.gray,
        marginTop: 16,
    },
});
