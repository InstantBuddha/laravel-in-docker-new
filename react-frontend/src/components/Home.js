import React from "react";
import { Link } from "react-router-dom";

function Home() {
    return (
        <div>
            <h1>This is Home</h1>

            <Link to="/login">
                <button type="button" className="btn btn-primary">
                    Go to Admin User Login
                </button>
            </Link>
            <Link to="/member-registration">
                <button type="button" className="btn btn-primary">
                    Go to new Member Registration Form
                </button>
            </Link>
        </div>
    );
}
export default Home;
