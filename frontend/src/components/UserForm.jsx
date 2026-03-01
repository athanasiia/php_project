import styles from '../styles/UserForm.module.css';
import SelectCountry from "./SelectCountry.jsx";
import {DB_VALUE} from "../constants/constants.js";

function UserForm({title, errors, submitButtonText, formData, handleSubmit, handleChange, isSubmitting, selectedSource}) {
    return (
        <form onSubmit={handleSubmit} className={styles.userForm}>
            <h2>{title}</h2>

            {errors && <div>{errors}</div>}
            <div>
                <label>Email</label>
                <input
                    type="email"
                    name="email"
                    value={formData.email}
                    onChange={handleChange}
                    placeholder="example@mail.com"
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

            {selectedSource === DB_VALUE &&
                <div>
                    <label>Country of residence</label>
                    <SelectCountry value={formData.country} onChange={handleChange} />
                </div>
            }

            {selectedSource === DB_VALUE &&
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
            }

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

            <button className={styles.submitButton} type="submit" disabled={isSubmitting}>{submitButtonText}</button>
        </form>
    );
}

export default UserForm;