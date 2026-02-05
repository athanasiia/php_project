import styles from '../styles/UserForm.module.css';
import {useState} from "react";
import SelectCountry from "../components/SelectCountry.jsx";
import {userFormErrors} from "../validation.js";
import {useNavigate} from "react-router-dom";
import {userService} from "../services/userService.js";

function UserFormPage() {
    const [formData, setFormData] = useState({
        email: '',
        name: '',
        country: '',
        city: '',
        gender: '',
        status: ''
    });

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
            const result = await userService.createUser(formData);

            setResultMessage('User created successfully!');
            setResultData({
                id: result.id,
                ...formData
            });
            setShowResultModal(true);
        } catch (error) {
            setResultMessage(error.message || 'Error creating user.');
            setResultData(null);
            setShowResultModal(true);
        } finally {
            setIsSubmitting(false);
        }
    };

    return (
        <div>
            <form method="post" onSubmit={handleSubmit} className={styles.userForm}>
                <h2>Create new user</h2>

                {errors && <div>{errors}</div>}
                <div>
                    <label>Email</label>
                    <input
                        type="email"
                        name="email"
                        value={formData.email}
                        onChange={handleChange}
                        placeholder="exapmle@mail.com"
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
                        placeholder="John Doe"
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
                        placeholder="New York"
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

                <button type="submit" disabled={isSubmitting}>Submit</button>
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

export default UserFormPage;