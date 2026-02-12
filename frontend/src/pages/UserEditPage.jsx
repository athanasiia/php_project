import {userFormErrors} from "../validation.js";
import {userService} from "../services/userService.js";

import {useEffect, useState} from "react";
import {useNavigate, useParams} from "react-router-dom";

import ResultModal from "../components/ResultModal.jsx";
import UserForm from "../components/UserForm.jsx";

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
        void loadUserData();
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
            setResultData(result);
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
            <UserForm
                title="Edit User"
                errors={errors}
                submitButtonText="Edit"
                formData={formData}
                handleSubmit={handleSubmit}
                handleChange={handleChange}
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

export default UserEditPage;