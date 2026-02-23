import styles from '../styles/UsersPage.module.css';

import {useState, useEffect} from 'react';
import { useNavigate } from 'react-router-dom';
import {userService} from "../services/userService.js";

import ResultModal from "../components/ResultModal.jsx";
import ConfirmModal from "../components/ConfirmModal.jsx";

function UsersPage() {
    const ITEMS_PER_PAGE = 5;
    const [users, setUsers] = useState([]);
    const [selectedIds, setSelectedIds] = useState(new Set());
    const [showConfirmModal, setShowConfirmModal] = useState(false);
    const [showResultModal, setShowResultModal] = useState(false);
    const [resultMessage, setResultMessage] = useState('');
    const [resultData, setResultData] = useState(null);
    const [isSubmitting, setIsSubmitting] = useState(false);
    const navigate = useNavigate();

    const [sortField, setSortField] = useState('id');
    const [sortOrder, setSortOrder] = useState('asc');
    const [filterStatus, setFilterStatus] = useState('all');
    const [filterGender, setFilterGender] = useState('all');
    const [searchTerm, setSearchTerm] = useState('');
    const [pageNumber, setPageNumber] = useState(1);

    useEffect(() => {
        void loadUsers();
    }, [filterStatus, filterGender, searchTerm, sortField, sortOrder, pageNumber]);

    const loadUsers = async () => {
        try {
            const filters = {
                status: filterStatus !== 'all' ? filterStatus : undefined,
                gender: filterGender !== 'all' ? filterGender : undefined,
                search: searchTerm || undefined,
                sort: sortField,
                order: sortOrder,
                limit: ITEMS_PER_PAGE,
                offset: (pageNumber - 1) * ITEMS_PER_PAGE
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

    const handleSelectAll = () => {
        if (selectedIds.size === users.length) {
            setSelectedIds(new Set());
        } else {
            setSelectedIds(new Set(users.map(user => user.id)));
        }
    };

    const handleGoToHome = () => {
        navigate('/');
    };

    const handleDeleteSelected = async () => {
        if (selectedIds.size === 0) return;

        setIsSubmitting(true);
        try {
            const idsToDelete = Array.from(selectedIds);

            const result = await userService.deleteUsers(idsToDelete);

            setResultMessage('Deleted successfully!');
            setResultData(result);

            setUsers(prevUsers =>
                prevUsers.filter(user => !selectedIds.has(user.id))
            );
            setSelectedIds(new Set());
            setShowConfirmModal(false);
            setShowResultModal(true);

        } catch (error) {
            console.error('Error deleting users:', error);
            alert('Failed to delete users');
        } finally {
            setIsSubmitting(false);
        }
    };

    const handlePageChange = (pageNumber, next) => {
        if (next) {
            setPageNumber(pageNumber + 1);
        } else {
            setPageNumber(pageNumber === 1 ? 1 : pageNumber - 1);
        }
    }

    if (!users) return <div>Users not found</div>;

    return (
        <div className={styles.usersContainer}>
            <div className={styles.tableHeader}>
                <h2>Users Table</h2>
                <div className={styles.deletePanel}>
                    <button
                        className={styles.deleteButton}
                        disabled={selectedIds.size === 0}
                        onClick={() => setShowConfirmModal(true)}
                    >
                        Delete Selected ({selectedIds.size})
                    </button>
                    <div>
                        <label>Check all</label>
                        <input
                            type="checkbox"
                            checked={selectedIds.size === users.length && users.length > 0}
                            onChange={handleSelectAll}
                        /></div>
                </div>
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

            <div>
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
                                    className={styles.tableButton}
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

                <div className={styles.tablePages}>
                    {pageNumber !== 1 &&
                        <button
                            className={styles.tableButton}
                            onClick={() => handlePageChange(pageNumber, 0)}
                        >
                            &#10094;
                        </button>
                    }
                    <p>{pageNumber}</p>
                    {users.length === ITEMS_PER_PAGE &&
                        <button
                            className={styles.tableButton}
                            onClick={() => handlePageChange(pageNumber, 1)}
                        >
                            &#10095;
                        </button>
                    }
                </div>
            </div>

            <ConfirmModal
                show={showConfirmModal}
                confirmationMessage={`Are you sure you want to delete ${selectedIds.size} ${selectedIds.size === 1 ? 'user' : 'users'}?`}
                action="Delete"
                onCancel={() => setShowConfirmModal(false)}
                onConfirm={handleDeleteSelected}
                isSubmitting={isSubmitting}
            />

            <ResultModal
                show={showResultModal}
                resultData={resultData}
                resultMessage={resultMessage}
                onClose={handleGoToHome}
            />
        </div>
    );
}

export default UsersPage;