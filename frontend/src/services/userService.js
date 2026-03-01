import {GOREST_VALUE} from "../constants/constants.js";

export const userService = {
    getAllUsers: async (filters = {}, selectedSource) => {
        try {
            const queryParams = new URLSearchParams();
            const { status, gender, search, sort, order, limit, offset } = filters;

            if (status && status !== 'all') queryParams.append('status', status);
            if (gender && gender !== 'all') queryParams.append('gender', gender);
            if (search) queryParams.append('search', search);
            if (sort) queryParams.append('sort', sort);
            if (order) queryParams.append('order', order);
            if (limit) queryParams.append('limit', limit);
            if (offset) queryParams.append('offset', offset);

            let url;
            if (selectedSource === GOREST_VALUE) {
                url = queryParams.toString() ? `/api/users?${queryParams}` : '/api/users';
            } else {
                url = queryParams.toString() ? `/users?${queryParams}` : '/users';
            }

            const response = await fetch(url);
            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);

            return await response.json() || [];
        } catch (error) {
            console.error('Error fetching users:', error);
            throw error;
        }
    },

    getUserById: async (id, selectedSource) => {
        const url = selectedSource === GOREST_VALUE ? `/api/users/${id}` : `/users/${id}`;

        try {
            const response = await fetch(url);
            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
            const data = await response.json();
            return data.data || data;
        } catch (error) {
            console.error('Error fetching user:', error);
            throw error;
        }
    },

    createUser: async (userData, selectedSource) => {
        const url = selectedSource === GOREST_VALUE ? '/api/users/create' : '/users/create';
        try {
            const response = await fetch(url, {
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

    updateUser: async (id, userData, selectedSource) => {
        const url = selectedSource === GOREST_VALUE ? `/api/users/${id}` : `/users/${id}`;

        try {
            const response = await fetch(url, {
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

    deleteUsers: async (userIds, selectedSource) => {
        const url = selectedSource === GOREST_VALUE ? `/api/users` : `/users`;

        try {
            const response = await fetch(url, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ ids: userIds })
            });

            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            const data = await response.json();

            if (!data.success) {
                throw new Error(data.message || 'Failed to delete users');
            }

            return data.data;
        } catch (error) {
            console.error('Error deleting users:', error);
            throw error;
        }
    }
};