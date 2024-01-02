import './App.css';
import { BrowserRouter as Router, Route, Routes } from "react-router-dom";
import Home from './components/Home';
import Login from './components/Login';
import ViewMembers from './components/ViewMembers';
import Signup from './components/formik-member-signup/Signup';
import Success from './components/formik-member-signup/Success';
import Failure from './components/formik-member-signup/Failure';

function App() {
  return (
    <Router>
      <div className="App">
        <Routes>
          <Route path="/" element={<Home />} />
          <Route path="/login" element={<Login />} />
          <Route path="/view-members" element={<ViewMembers />} />
          <Route path="/member-registration" element={<Signup />} />
          <Route path="/success" element={<Success />} />
          <Route path="/failure/:errorCode" element={<Failure />} />
        </Routes>
      </div>
    </Router>
  );
}

export default App;
