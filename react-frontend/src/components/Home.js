import React from "react";
import { Link } from "react-router-dom";

function Home() {
    return (
        <div>
            <h1>This is Home</h1>

            <Link to="/login">
                <button type="button" class="btn btn-primary">
                    Go to Login
                </button>
            </Link>
        </div>
    );
}
export default Home;
