import styles from '../styles/UserForm.module.css';
import {userService} from "../services/userService.js";
import {useEffect, useState} from "react";
import {useNavigate, useParams} from "react-router-dom";
import SelectCountry from "../components/SelectCountry.jsx";
import {userFormErrors} from "../validation.js";

function UserEditPage() {
    const { id } = useParams();
    const [formData, setFormData] = useState(null);

    const [errors, setErrors] = useState('');
    const [showResultModal, setShowResultModal] = useState(false);
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [resultMessage, setResultMessage] = useState('');
    const [resultData, setResultData] = useState(null);
    const navigate = useNavigate();

    const validate = () => {
        const error = userFormErrors(formData);
        setErrors(error);
        return !error;
    }

    useEffect(() => {
        loadUserData();
    }, [id]);

    const loadUserData = async () => {
        try {
            const user = await userService.getUserById(id);
            setFormData({
                email: user.email,
                name: user.name,
                country: user.country,
                city: user.city,
                gender: user.gender,
                status: user.status
            });
        } catch (error) {
            console.error('Error loading user:', error);
            navigate('/');
        }
    };

    const handleChange = (e) => {
        const {name, value} = e.target;
        setFormData({
            ...formData,
            [name]: value
        });
        setErrors('');
    };

    const handleGoToHome = () => {
        navigate('/');
    };

    const handleSubmit = async (e) => {
        e.preventDefault();

        if (!validate()) return;

        setIsSubmitting(true);

        try {
            const result = userService.updateUser(id, formData);

            setResultMessage('User updated successfully!');
            setResultData({
                id: result.id,
                ...formData
            });
            setShowResultModal(true);
        } catch (error) {
            setResultMessage(error.message || 'Error updating user.');
            setResultData(null);
            setShowResultModal(true);
        } finally {
            setIsSubmitting(false);
        }
    };


    if (!formData) return <div>User not found</div>;

    return (
        <div>
            <form onSubmit={handleSubmit} className={styles.userForm}>
                <h2>Edit User</h2>

                {errors && <div>{errors}</div>}
                <div>
                    <label>Email</label>
                    <input
                        type="email"
                        name="email"
                        value={formData.email}
                        onChange={handleChange}
                        placeholder={formData.email}
                        required
                    />
                </div>

                <div>
                    <label>Your first and last name</label>
                    <input
                        type="text"
                        name="name"
                        value={formData.name}
                        onChange={handleChange}
                        placeholder={formData.name}
                        required
                    />
                </div>

                <div>
                    <label>Country of residence</label>
                    <SelectCountry value={formData.country} onChange={handleChange} />
                </div>

                <div>
                    <label>City</label>
                    <input
                        type="text"
                        name="city"
                        value={formData.city}
                        onChange={handleChange}
                        placeholder={formData.city}
                        required
                    />
                </div>

                <div>
                    <label>Gender</label>
                    <select name="gender" value={formData.gender} onChange={handleChange} required>
                        <option value="">Select gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                </div>

                <div>
                    <label>Status</label>
                    <select name="status" value={formData.status} onChange={handleChange} required>
                        <option value="">Select status</option>
                        <option value="active">Active user</option>
                        <option value="inactive">Inactive user</option>
                    </select>
                </div>

                <button type="submit" disabled={isSubmitting}>Edit</button>
            </form>

            {showResultModal && (
                <div className={styles.modalOverlay}>
                    <div className={styles.modal}>
                        <div className={styles.modalContent}>
                            <h3>{resultData ? 'Success!' : 'Error'}</h3>
                            <p>{resultMessage}</p>

                            <button
                                className={styles.modalButton}
                                onClick={handleGoToHome}
                            >Return to home page</button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
}

export default UserEditPage;