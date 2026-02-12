import {userFormErrors} from "../validation.js";
import {userService} from "../services/userService.js";

import {useState} from "react";
import {useNavigate} from "react-router-dom";

import ResultModal from "../components/ResultModal.jsx";
import UserForm from "../components/UserForm.jsx";

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
            setResultData(result);
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
            <UserForm
                title="Create new User"
                errors={errors}
                submitButtonText="Submit"
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

export default UserFormPage;