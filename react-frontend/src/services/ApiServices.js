import axios from "axios";
//Clean and use file when everything works
export const axiosInstance = axios.create({
    baseURL: "http://localhost/api",
    //withCredentials: true, // Allow sending cookies when making requests
    headers: {
        Accept: "application/json",
        "Content-Type": "application/json",
    },
});

export const handleSubmit = async (credentials, setSubmitting) => {
    const sanitizedValues = Object.fromEntries(
        Object.entries(credentials).filter(([key, value]) => value !== "")
    );

    try {
        const response = await axiosInstance.post("/members", sanitizedValues);
        console.log("Response:", response.data);
        setSubmitting(false);
    } catch (error) {
        console.error(
            "Error:",
            error.response ? error.response.data : error.message
        );

        setSubmitting(false);
    }
};

export const sendStuff = async (credentials, setSubmitting) => {
    const url = "http://localhost/api/members";
    const headers = {
        Accept: "application/json",
        "Content-Type": "application/x-www-form-urlencoded",
        //'Content-Length': 9,  //set it later
    };
    await axios
        .post(url, credentials, { headers: headers })
        .then((response) => {
            console.log(response);
            setSubmitting(false);
        })
        .catch((error) => {
            console.log(credentials);
            console.log(error.message);
            setSubmitting(false);
        });
};
