import styles from  '../styles/HomePage.module.css';
import {Link} from "react-router-dom";

function HomePage() {
    return (
        <div className={styles.home}>
            <Link to="/users/new">
                <button className={styles.homeButton}>Add new user</button>
            </Link>

            <Link to="/users">
                <button className={styles.homeButton}>Show all users</button>
            </Link>
        </div>
    );
}

export default HomePage;