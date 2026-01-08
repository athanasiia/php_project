import './UserForm.css';
import {useState} from "react";
import SelectCountry from "../components/SelectCountry.jsx";
import {userFormErrors} from "../validation.js";
import {useNavigate} from "react-router-dom";

function UserForm() {
    const [formData, setFormData] = useState({
        email: '',
        name: '',
        country: '',
        city: '',
        gender: '',
        status: ''
    });

    const [errors, setErrors] = useState('');
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

    const handleSubmit = async (e) => {
        e.preventDefault();

        if (!validate()) return;

        const response = await fetch('http://project.local/users/create', {
            method: 'POST',
            headers : {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(formData)
        });

        const result = await response.json();

        if (result.success) {
            navigate('/users/result', {
                state: {userData: formData}
            });
        } else {
            setErrors('Error creating user');
        }
    }

    return (
        <form method="post" onSubmit={handleSubmit} className="user-form">
            <h2>Create new user</h2>

            <div style={{color: 'red', display: errors ? 'block' : 'none'}}>{errors}</div>
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

            <button type="submit">Submit</button>
        </form>
    );
}

export default UserForm;