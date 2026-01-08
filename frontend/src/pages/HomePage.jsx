import './HomePage.css';
import {Link} from "react-router-dom";

function HomePage() {
    return (
        <div className="home">
            <Link to="/users/new">
                <button className="home-button">Add new user</button>
            </Link>
        </div>
    );
}

export default HomePage;