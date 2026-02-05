import styles from '../styles/UsersPage.module.css';

import {useState, useEffect} from 'react';
import { useNavigate } from 'react-router-dom';
import {userService} from "../services/userService.js";

function UsersPage() {
    const [users, setUsers] = useState([]);
    const [selectedIds, setSelectedIds] = useState(new Set());
    const [showConfirmModal, setShowConfirmModal] = useState(false);
    const navigate = useNavigate();

    const [sortField, setSortField] = useState('id');
    const [sortOrder, setSortOrder] = useState('asc');
    const [filterStatus, setFilterStatus] = useState('all');
    const [filterGender, setFilterGender] = useState('all');
    const [searchTerm, setSearchTerm] = useState('');

    useEffect(() => {
        loadUsers();
    }, [filterStatus, filterGender, searchTerm, sortField, sortOrder]);

    const loadUsers = async () => {
        try {
            const filters = {
                status: filterStatus !== 'all' ? filterStatus : undefined,
                gender: filterGender !== 'all' ? filterGender : undefined,
                search: searchTerm || undefined,
                sort: sortField,
                order: sortOrder
            };

            Object.keys(filters).forEach(key =>
                filters[key] === undefined && delete filters[key]
            );

            const fetchedUsers = await userService.getAllUsers(filters);
            setUsers(fetchedUsers);
            setSelectedIds(new Set());

        } catch (error) {
            console.error('Error loading users:', error);
            alert('Failed to load users');
        }
    };

    const handleSort = (field) => {
        if (sortField === field) {
            setSortOrder(sortOrder === 'asc' ? 'desc' : 'asc');
        } else {
            setSortField(field);
            setSortOrder('asc');
        }
    };

    const getSortIcon = (field) => {
        if (sortField !== field) return '↕';
        return sortOrder === 'asc' ? '↑' : '↓';
    };

    const handleEdit = (userId) => {
        navigate(`/users/${userId}/edit`);
    };

    const handleCheckboxChange = (userId) => {
        const newSelected = new Set(selectedIds);
        if (newSelected.has(userId)) {
            newSelected.delete(userId);
        } else {
            newSelected.add(userId);
        }
        setSelectedIds(newSelected);
    };

    const handleDeleteSelected = async () => {
        if (selectedIds.size === 0) return;

        try {
            const idsToDelete = Array.from(selectedIds);

            await userService.deleteUsers(idsToDelete);

            setUsers(prevUsers =>
                prevUsers.filter(user => !selectedIds.has(user.id))
            );
            setSelectedIds(new Set());
            setShowConfirmModal(false);

        } catch (error) {
            console.error('Error deleting users:', error);
            alert('Failed to delete users');
        }
    };

    if (!users) return <div>Users not found</div>;

    return (
        <div className={styles.usersContainer}>
            <div className={styles.tableHeader}>
                <h2>Users Table</h2>
                {selectedIds.size > 0 && (
                    <button
                        className={styles.deleteButton}
                        onClick={() => setShowConfirmModal(true)}
                    >
                        Delete Selected ({selectedIds.size})
                    </button>
                )}
            </div>

            <div className={styles.filtersPanel}>
                <div className={styles.searchBox}>
                    <input
                        type="text"
                        placeholder="Search by name..."
                        value={searchTerm}
                        onChange={(e) => setSearchTerm(e.target.value)}
                        className={styles.searchInput}
                    />
                </div>

                <div className={styles.filterGroup}>
                    <label>Status:</label>
                    <select
                        value={filterStatus}
                        onChange={(e) => setFilterStatus(e.target.value)}
                        className={styles.filterSelect}
                    >
                        <option value="all">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <div className={styles.filterGroup}>
                    <label>Gender:</label>
                    <select
                        value={filterGender}
                        onChange={(e) => setFilterGender(e.target.value)}
                        className={styles.filterSelect}
                    >
                        <option value="all">All Genders</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                </div>

                <div className={styles.sortButtons}>
                    <span>Sort by:</span>
                    <button
                        className={styles.sortButton}
                        onClick={() => handleSort('name')}
                    >
                        Name {getSortIcon('name')}
                    </button>
                    <button
                        className={styles.sortButton}
                        onClick={() => handleSort('email')}
                    >
                        Email {getSortIcon('email')}
                    </button>
                    <button
                        className={styles.sortButton}
                        onClick={() => handleSort('id')}
                    >
                        ID {getSortIcon('id')}
                    </button>
                </div>
            </div>

            <table className={styles.usersTable}>
                <thead>
                <tr>
                    <th></th>
                    <th>Delete</th>
                    <th>ID</th>
                    <th>Email</th>
                    <th>Name</th>
                    <th>City</th>
                    <th>Country</th>
                    <th>Gender</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                {users.map(user => (
                    <tr key={user.id}>
                        <td>
                            <button
                                className={styles.editButton}
                                onClick={() => handleEdit(user.id)}
                            >
                                Edit
                            </button>
                        </td>
                        <td>
                            <input
                                type="checkbox"
                                checked={selectedIds.has(user.id)}
                                onChange={() => handleCheckboxChange(user.id)}
                            />
                        </td>
                        <td>{user.id}</td>
                        <td>{user.email}</td>
                        <td>{user.name}</td>
                        <td>{user.city}</td>
                        <td>{user.country}</td>
                        <td>{user.gender}</td>
                        <td>{user.status}</td>
                    </tr>
                ))}
                </tbody>
            </table>

            {showConfirmModal && (
                <div className={styles.modalOverlay}>
                    <div className={styles.modal}>
                        <div className={styles.modalContent}>
                            <h3>Confirm Deletion</h3>
                            <p>
                                Are you sure you want to delete {selectedIds.size}
                                {selectedIds.size === 1 ? ' user' : ' users'}?
                            </p>
                            <div className={styles.modalActions}>
                                <button
                                    className={styles.modalButton}
                                    onClick={() => setShowConfirmModal(false)}
                                >
                                    Cancel
                                </button>
                                <button
                                    className={styles.modalButton}
                                    onClick={handleDeleteSelected}
                                >
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
}

export default UsersPage;