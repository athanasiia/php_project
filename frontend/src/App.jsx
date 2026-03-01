import { BrowserRouter, Routes, Route } from 'react-router-dom';
import UserFormPage from "./pages/UserFormPage.jsx";
import UsersPage from "./pages/UsersPage.jsx";
import UserEditPage from "./pages/UserEditPage.jsx"

function App() {
    return (
        <BrowserRouter>
            <Routes>
                <Route path="/" element={<UsersPage />} />
                <Route path="/users/new" element={<UserFormPage selectedSource='db'/>} />
                <Route path="/users/:id/edit" element={<UserEditPage selectedSource='db'/>} />

                <Route path="/api/users/new" element={<UserFormPage selectedSource='gorest'/>} />
                <Route path="/api/users/:id/edit" element={<UserEditPage selectedSource='gorest'/>}/>
            </Routes>
        </BrowserRouter>
    )
}
export default App;
