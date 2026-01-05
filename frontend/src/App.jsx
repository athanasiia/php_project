import React from "react";
import { BrowserRouter, Routes, Route } from 'react-router-dom';
import HomePage from "./pages/HomePage.jsx";
import UserForm from "./pages/UserForm.jsx";
import ResultPage from "./pages/ResultPage.jsx";

function App() {
    return (
        <BrowserRouter>
            <Routes>
                <Route path="/" element={<HomePage />} />
                <Route path="/users/new" element={<UserForm />} />
                <Route path="/users/result" element={<ResultPage />} />
            </Routes>
        </BrowserRouter>
    )
}
export default App;
