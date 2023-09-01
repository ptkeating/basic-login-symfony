import { useReducer, useState } from "react";
import registerFormReducer, { registerFormErrorReducer } from "../reducers/registerFormReducer";
import RegisterForm from "../form-objects/registerForm";
import { register } from "../services/registerService";
import { API_BASE, handleUnprocessableError } from "../services/apiService";
import { Link, useNavigate } from "react-router-dom";
import { UnprocessableError } from "../types/unprocessableError";
import { PropsWithChildren } from 'react';
type SuccessfulHandle = {
    handleSuccess: any;
}
export default function Register(props: PropsWithChildren<SuccessfulHandle>) {
    // Set the initial state of the form
    const formAction: string = API_BASE + '/api/register';
    const [formData, setFormData] = useReducer(registerFormReducer, new RegisterForm());
    const [submitting, setSubmitting] = useState(false);
    const [empty, setEmpty] = useState(true);
    const [errors, setErrors] = useReducer(registerFormErrorReducer, new RegisterForm());

    const navigate = useNavigate();
    const handleSubmit = (event: any) => {
        event.preventDefault();
        setSubmitting(true);
        setErrors({ name: 'email', value: '', reset: true });
        register(formData)
            .then(() => {
                // direct user to login page
                props.handleSuccess('register');
                navigate('/login');
                // console.log(data);
            })
            .catch((error) => {
                if (error.status && error.status === 422) {
                    error.json().then((data: UnprocessableError) => {
                        setErrors({ name: 'email', value: '', reset: true });
                        const newErrors = handleUnprocessableError(data);
                        for (const newError of newErrors) {
                            setErrors(newError);
                        }
                    });
                }
                // process the errors and display them
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
                <div className="py-5">
                    <h1 className="text-3xl font-semibold text-center">Create an account</h1>
                </div>
                <div className="py-2">
                    <fieldset disabled={submitting}>
                        <input
                            name="full_name"
                            id="register-full-name-input"
                            type="text"
                            placeholder="Name"
                            className={(errors.full_name ? 'border-red-400 ' : 'border-gray-400 ') + 'form-input px-4 py-3 border rounded-lg w-96'}
                            onChange={handleChange}
                            value={formData.full_name || ''}
                        />
                        {errors.full_name && (
                            <div className="text-red-600 line-broken-errors">
                                {errors.full_name}
                            </div>
                        )}

                    </fieldset>
                </div>
                <div className="py-2">
                    <fieldset disabled={submitting}>
                        <input
                            name="email"
                            id="register-email-input"
                            type="email"
                            placeholder="Enter your email"
                            className={(errors.email ? 'border-red-400 ' : 'border-gray-400 ') + 'form-input px-4 py-3 border rounded-lg w-96'}
                            onChange={handleChange}
                            value={formData.email || ''}
                        />
                        {errors.email && (
                            <div className="text-red-600 line-broken-errors">
                                {errors.email}
                            </div>
                        )}

                    </fieldset>
                </div>
                <div className="py-2">
                    <fieldset disabled={submitting}>
                        <input
                            name="password"
                            id="register-password-input"
                            type="password"
                            placeholder="Password"
                            className={((errors.raw_password || errors.password) ? 'border-red-400 ' : 'border-gray-400 ') + 'form-input px-4 py-3 border rounded-lg w-96'}
                            onChange={handleChange}
                            value={formData.password || ''}
                        />
                        {errors.raw_password && (
                            <div className="line-broken-errors text-red-600">
                                {errors.raw_password}
                            </div>
                        )}
                        {errors.password && (
                            <div className="line-broken-errors text-red-600">
                                {errors.password}
                            </div>
                        )}

                    </fieldset>
                </div>
                <div className="py-2">
                    <button type="submit" className="button border border-gray-400 rounded-lg w-96 shadow-sm cursor-pointer px-12 py-2 bg-orange-600 text-white text-lg font-medium" disabled={empty || submitting}>Get started</button>
                </div>
            </form>
            <div className="pt-5">
                <p className="font-normal text-center">Already have an account? <Link to="/login" className="text-orange-600">Log in</Link></p>
            </div>
        </div>
    );
}