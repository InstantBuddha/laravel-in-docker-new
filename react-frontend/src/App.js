import './App.css';
import { BrowserRouter as Router, Route, Routes } from "react-router-dom";
import Home from './components/Home';
import Login from './components/Login';
import ViewMembers from './components/ViewMembers';

function App() {
  return (
    <Router>
      <div>
        <Routes>
          <Route path="/" element={<Home />} />
          <Route path="/login" element={<Login />} />
          <Route path="/view-members" element={<ViewMembers />} />
        </Routes>
      </div>
    </Router>
  );
}

export default App;
