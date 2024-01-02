import React from "react";
import { useParams } from "react-router-dom";

function Failure() {
    const { errorCode } = useParams();

    return (
        <div>
            <h1>Failure</h1>
            <h2>Error code: {errorCode}</h2>
        </div>
    );
}
export default Failure;
