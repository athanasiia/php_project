const isValidEmail = (email) => {
    return /\S+@\S+\.\S+/.test(email);
}

export const userFormErrors = (formData) => {
    const required = ['email', 'name', 'country', 'city', 'gender', 'status'];
    const missing = required.filter(field => !formData[field]);

    if (missing.length > 0) {
        return 'Please fill in all of the fields';
    }
    
    if (!isValidEmail(formData.email.trim())) {
        return 'Email is invalid';
    }

    return '';
}