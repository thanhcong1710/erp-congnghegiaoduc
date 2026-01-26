import React from 'react';
import {
    View,
    Text,
    StyleSheet,
    ScrollView,
    TouchableOpacity,
    Image,
} from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { Ionicons } from '@expo/vector-icons';
import { useAppDispatch, useAppSelector } from '../../store/hooks';
import { logout } from '../../store/slices/authSlice';
import { COLORS } from '../../constants/colors';

export default function StudentProfileScreen() {
    const dispatch = useAppDispatch();
    const { user } = useAppSelector((state) => state.auth);

    const handleLogout = () => {
        dispatch(logout());
    };

    return (
        <SafeAreaView style={styles.container}>
            <ScrollView>
                <View style={styles.header}>
                    <View style={styles.avatarContainer}>
                        {user?.avatar ? (
                            <Image source={{ uri: user.avatar }} style={styles.avatar} />
                        ) : (
                            <View style={styles.avatarPlaceholder}>
                                <Text style={styles.avatarText}>
                                    {user?.full_name?.charAt(0) || 'S'}
                                </Text>
                            </View>
                        )}
                    </View>
                    <Text style={styles.name}>{user?.full_name || 'Học sinh'}</Text>
                    <Text style={styles.email}>{user?.email || ''}</Text>
                </View>

                <View style={styles.menuContainer}>
                    <TouchableOpacity style={styles.menuItem}>
                        <Ionicons name="person-outline" size={24} color={COLORS.student.primary} />
                        <Text style={styles.menuText}>Thông tin cá nhân</Text>
                        <Ionicons name="chevron-forward" size={24} color={COLORS.gray} />
                    </TouchableOpacity>

                    <TouchableOpacity style={styles.menuItem}>
                        <Ionicons name="lock-closed-outline" size={24} color={COLORS.student.primary} />
                        <Text style={styles.menuText}>Đổi mật khẩu</Text>
                        <Ionicons name="chevron-forward" size={24} color={COLORS.gray} />
                    </TouchableOpacity>

                    <TouchableOpacity style={styles.menuItem}>
                        <Ionicons name="notifications-outline" size={24} color={COLORS.student.primary} />
                        <Text style={styles.menuText}>Thông báo</Text>
                        <Ionicons name="chevron-forward" size={24} color={COLORS.gray} />
                    </TouchableOpacity>

                    <TouchableOpacity style={styles.menuItem}>
                        <Ionicons name="settings-outline" size={24} color={COLORS.student.primary} />
                        <Text style={styles.menuText}>Cài đặt</Text>
                        <Ionicons name="chevron-forward" size={24} color={COLORS.gray} />
                    </TouchableOpacity>

                    <TouchableOpacity style={styles.menuItem} onPress={handleLogout}>
                        <Ionicons name="log-out-outline" size={24} color={COLORS.error} />
                        <Text style={[styles.menuText, { color: COLORS.error }]}>Đăng xuất</Text>
                        <Ionicons name="chevron-forward" size={24} color={COLORS.gray} />
                    </TouchableOpacity>
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
        alignItems: 'center',
        paddingVertical: 32,
        marginBottom: 16,
    },
    avatarContainer: {
        marginBottom: 16,
    },
    avatar: {
        width: 100,
        height: 100,
        borderRadius: 50,
    },
    avatarPlaceholder: {
        width: 100,
        height: 100,
        borderRadius: 50,
        backgroundColor: COLORS.student.primary,
        justifyContent: 'center',
        alignItems: 'center',
    },
    avatarText: {
        fontSize: 40,
        fontWeight: 'bold',
        color: COLORS.white,
    },
    name: {
        fontSize: 24,
        fontWeight: 'bold',
        color: COLORS.textPrimary,
        marginBottom: 4,
    },
    email: {
        fontSize: 14,
        color: COLORS.textSecondary,
    },
    menuContainer: {
        backgroundColor: COLORS.white,
        paddingHorizontal: 16,
    },
    menuItem: {
        flexDirection: 'row',
        alignItems: 'center',
        paddingVertical: 16,
        borderBottomWidth: 1,
        borderBottomColor: COLORS.border,
    },
    menuText: {
        flex: 1,
        fontSize: 16,
        color: COLORS.textPrimary,
        marginLeft: 16,
    },
});
