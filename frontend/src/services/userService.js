export const userService = {
    getAllUsers: async (filters = {}) => {
        try {
            const queryParams = new URLSearchParams();

            if (filters.status && filters.status !== 'all') {
                queryParams.append('status', filters.status);
            }

            if (filters.gender && filters.gender !== 'all') {
                queryParams.append('gender', filters.gender);
            }

            if (filters.search) {
                queryParams.append('search', filters.search);
            }

            if (filters.sort) {
                queryParams.append('sort', filters.sort);
            }

            if (filters.order) {
                queryParams.append('order', filters.order);
            }

            const queryString = queryParams.toString();
            const url = queryString ? `/api/users?${queryString}` : '/api/users';

            const response = await fetch(url);
            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);

            const data = await response.json();
            return data || [];

        } catch (error) {
            console.error('Error fetching users:', error);
            throw error;
        }
    },

    getUserById: async (id) => {
        try {
            const response = await fetch(`/api/users/${id}`);
            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
            const data = await response.json();
            return data.data || data;
        } catch (error) {
            console.error('Error fetching user:', error);
            throw error;
        }
    },

    createUser: async (userData) => {
        try {
            const response = await fetch('/api/users/create', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(userData)
            });

            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }

            const data = await response.json();

            if (!data.success) {
                throw new Error(data.message || 'Failed to create user');
            }

            return data.data;

        } catch (error) {
            console.error('Error creating user:', error);
            throw error;
        }
    },

    updateUser: async (id, userData) => {
        try {
            const response = await fetch(`/api/users/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(userData)
            });
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            const data = await response.json();

            if (!data.success) {
                throw new Error(data.message || 'Failed to update user');
            }

            return data.data;
        } catch (error) {
            console.error('Error updating user:', error);
            throw error;
        }
    },

    deleteUsers: async (userIds) => {
        try {
            const deletePromises = userIds.map(id =>
                fetch(`/api/users/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                }).then(response => {
                    if (!response.ok) {
                        throw new Error(`Failed to delete user ${id}`);
                    }
                    return response.json();
                })
            );

            return await Promise.allSettled(deletePromises);

        } catch (error) {
            console.error('Error deleting users:', error);
            throw error;
        }
    }
};