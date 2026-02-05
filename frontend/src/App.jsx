import { BrowserRouter, Routes, Route } from 'react-router-dom';
import HomePage from "./pages/HomePage.jsx";
import UserFormPage from "./pages/UserFormPage.jsx";
import UsersPage from "./pages/UsersPage.jsx";
import UserEditPage from "./pages/UserEditPage.jsx"

function App() {
    return (
        <BrowserRouter>
            <Routes>
                <Route path="/" element={<HomePage />} />
                <Route path="/users/new" element={<UserFormPage />} />
                <Route path="/users" element={<UsersPage />} />
                <Route path="/users/:id/edit" element={<UserEditPage />} />
            </Routes>
        </BrowserRouter>
    )
}
export default App;
