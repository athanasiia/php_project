import './ResultPage.css';
import React from "react";
import {useLocation} from "react-router-dom";

function ResultPage() {
    const location = useLocation();

    const userData = location.state?.userData;

    if (!userData) {
        return (
            <div>
                No data available
            </div>
        );
    }

    return (
        <div className='result'>
            <ul>
                <li><strong>Email:</strong> {userData.email}</li>
                <li><strong>Name:</strong> {userData.name}</li>
                <li><strong>Country:</strong> {userData.country}</li>
                <li><strong>City:</strong> {userData.city}</li>
                <li><strong>Gender:</strong> {userData.gender}</li>
                <li><strong>Status:</strong> {userData.status}</li>
            </ul>
        </div>
    );
}

export default ResultPage;