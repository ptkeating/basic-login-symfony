import { PropsWithChildren, useReducer, useState } from "react";
import loginFormReducer, { loginFormErrorReducer } from "../reducers/loginFormReducer";
import LoginForm from "../form-objects/loginForm";
import { API_BASE } from "../services/apiService";
import { login } from "../services/loginService";
import { Link, useNavigate } from "react-router-dom";
type SuccessfulHandle = {
    handleLogin: any;
}
export default function Login(props: PropsWithChildren<SuccessfulHandle>) {
    // Set the initial state of the form
    const formAction: string = API_BASE + '/api/login_check';
    const [formData, setFormData] = useReducer(loginFormReducer, new LoginForm());
    const [submitting, setSubmitting] = useState(false);
    const [empty, setEmpty] = useState(true);
    const [errors, setErrors] = useReducer(loginFormErrorReducer, new LoginForm());
    const navigate = useNavigate();
    const handleSubmit = (event: any) => {
        event.preventDefault();
        setSubmitting(true);
        setErrors({ name: 'email', value: '', reset: true });
        login(formData)
            .then(data => {
                // direct user to update-user page
                console.log('Successfully logged in');
                // store the token in localStorage
                localStorage.setItem('token', data.token);
                localStorage.setItem('user_id', data.user_id);
                props.handleLogin({ value: true });
                navigate('/update-user');
            })
            .catch((error) => {
                if (error.status && error.status === 401) {
                    setErrors({ value: 'Email and password do not match', name: 'password' });
                }
                // process the errors and display them
                // console.log(error);
            })
            .finally(() => {
                setSubmitting(false);
            });
    }
    const handleChange = (event: any) => {
        if (event.target.value) {
            setEmpty(false);
        } else {
            setEmpty(Object.entries(formData).reduce((empty, value) => {
                return empty && (value === undefined || value.length < 1);
            }, true));
        }
        setFormData({
            name: event.target.name,
            value: event.target.value,
        });
    }
    return (
        <div className="my-12 py-10">
            <form action={formAction} onSubmit={handleSubmit} method="post">
                <div className="text-center py-5">
                    <h1 className="text-3xl font-semibold">Log in to your account</h1>
                    <p className="py-2 text-gray-500">Welcome back! Please enter your details.</p>
                </div>
                <div className="py-2">
                    <fieldset disabled={submitting}>
                        <label htmlFor="login-email-input" className="capitalize block pb-1 mr-auto">email</label>
                        <input
                            name="email"
                            id="login-email-input"
                            type="email"
                            placeholder="Enter your email"
                            className={(errors.email ? 'border-red-400 ' : 'border-gray-400 ') + 'form-input px-4 py-3 border rounded-lg w-96'}
                            onChange={handleChange}
                            value={formData.email || ''}
                        />
                        {errors.email && (
                            <div className="text-red-600">
                                {errors.email}
                            </div>
                        )}
                    </fieldset>
                </div>
                <div className="py-2">
                    <fieldset disabled={submitting}>
                        <div className="pb-2">
                            <label htmlFor="login-password-input" className="capitalize block">password</label>
                        </div>
                        <input
                            name="password"
                            id="login-password-input"
                            type="password"
                            placeholder="Password"
                            className={(errors.password ? 'border-red-400 ' : 'border-gray-400 ') + 'form-input px-4 py-3 border rounded-lg w-96'}
                            onChange={handleChange}
                            value={formData.password || ''}
                        />
                        {errors.password && (
                            <div className="text-red-600">
                                {errors.password}
                            </div>
                        )}
                    </fieldset>
                </div>
                <div className="py-2">
                    <button type="submit" className="button border border-gray-400 rounded-lg w-96 shadow-sm cursor-pointer px-12 py-2 bg-orange-600 text-white text-lg font-medium" disabled={empty || submitting}>Sign in</button>
                </div>
            </form>
            <div className="pt-5">
                <p className="font-normal text-center">Don't have an account? <Link to="/register" className="text-orange-600">Sign up</Link></p>
            </div>
        </div>
    );
}