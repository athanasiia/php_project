import './HomePage.css';
import React from "react";
import {Link} from "react-router-dom";

function HomePage() {
    return (
        <div>
            <Link to="/users/new">
                <button>Add new user</button>
            </Link>
        </div>
    );
}

export default HomePage;