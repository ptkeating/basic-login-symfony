import { useEffect, useReducer, useState } from "react";
import { API_BASE, handleUnprocessableError } from "../services/apiService";
import updateUserFormReducer, { updateUserFormErrorReducer } from "../reducers/updateUserFormReducer";
import UpdateUserForm from "../form-objects/updateUserForm";
import { updateUser, getUser } from "../services/updateUserService";
import { PropsWithChildren } from 'react';
import { UnprocessableError } from "../types/unprocessableError";
type SuccessfulHandle = {
    handleSuccess: any;
}
export default function UpdateUser(props: PropsWithChildren<SuccessfulHandle>) {
    // Set the initial state of the form
    const formAction: string = API_BASE + '/api/users';
    const [formData, setFormData] = useReducer(updateUserFormReducer, new UpdateUserForm());
    const [submitting, setSubmitting] = useState(false);
    const [empty, setEmpty] = useState(true);
    const [errors, setErrors] = useReducer(updateUserFormErrorReducer, new UpdateUserForm());

    const handleSubmit = (event: any) => {
        event.preventDefault();
        setSubmitting(true);
        setErrors({ name: 'email', value: '', reset: true });
        updateUser(formData)
            .then(() => {
                // pop up a success modal
                props.handleSuccess(true);
            })
            .catch((error) => {
                if (error.status && error.status === 422) {
                    error.json().then((data: UnprocessableError) => {
                        setErrors({ name: 'full_name', value: '', reset: true });
                        const newErrors = handleUnprocessableError(data);
                        for (const newError of newErrors) {
                            setErrors(newError);
                        }
                    });
                }
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
    useEffect(() => {
        getUser()
            .then((data) => {
                let user: UpdateUserForm = data.data;
                for (const [prop, value] of Object.entries(user)) {
                    setFormData({ name: prop, value: value });
                }
            })
    }, []);
    return (
        <div className="my-12 py-10 min-w-full px-12 flex flex-row justify-center">
            <form className="px-12 w-2/3" action={formAction} onSubmit={handleSubmit} method="post">
                <div className="py-5 text-center">
                    <h1 className="text-3xl font-semibold">My Details</h1>
                    <p className="">Update your personal details</p>
                </div>
                <div className="py-2 flex flex-row justify-between">
                    <label htmlFor="update-user-full-name-input" className="capitalize">name</label>
                    <fieldset disabled={submitting}>
                        <input
                            name="full_name"
                            id="update-user-full-name-input"
                            type="text"
                            placeholder="Name"
                            className={(errors.full_name ? 'border-red-400 ' : 'border-gray-400 ') + 'form-input px-4 py-3 border rounded-lg w-96'}
                            onChange={handleChange}
                            value={formData.full_name || ''}
                        />
                        {errors.full_name && (
                            <div className="line-broken-errors text-red-600">
                                {errors.full_name}
                            </div>
                        )}
                    </fieldset>
                </div>
                <hr />
                <div className="py-2 flex flex-row justify-between ">
                    <label htmlFor="update-user-email-input" className="block">Email address</label>
                    <fieldset disabled={true}>
                        <input
                            name="email"
                            id="update-user-email-input"
                            type="email"
                            className="form-input px-4 py-3 border border-gray-400 rounded-lg w-96"
                            defaultValue={formData.email || ''}
                        />
                    </fieldset>
                </div>
                <hr />
                <div className="py-2 flex flex-row justify-between ">
                    <label htmlFor="update-user-house-number-input" className="">House number</label>
                    <fieldset disabled={submitting}>
                        <input
                            name="house_number"
                            id="update-user-house-number-input"
                            type="text"
                            placeholder="e.g. 100B"
                            className={(errors.house_number ? 'border-red-400 ' : 'border-gray-400 ') + 'form-input px-4 py-3 border rounded-lg w-96'}
                            onChange={handleChange}
                            value={formData.house_number || ''}
                        />
                        {errors.house_number && (
                            <div className="line-broken-errors text-red-600">
                                {errors.house_number}
                            </div>
                        )}
                    </fieldset>
                </div>
                <hr />
                <div className="py-2 flex flex-row justify-between">
                    <label htmlFor="update-user-street-address-input" className="">Street Address</label>
                    <fieldset disabled={submitting}>
                        <input
                            name="street_address"
                            id="update-user-street-address-input"
                            type="text"
                            placeholder="Street Name"
                            className={(errors.street_address ? 'border-red-400 ' : 'border-gray-400 ') + 'form-input px-4 py-3 border rounded-lg w-96'}
                            onChange={handleChange}
                            value={formData.street_address || ''}
                        />
                        {errors.street_address && (
                            <div className="line-broken-errors text-red-600">
                                {errors.street_address}
                            </div>
                        )}

                    </fieldset>
                </div>
                <hr />
                <div className="py-2 flex flex-row justify-between">
                    <label htmlFor="update-user-city-input" className="">City</label>
                    <fieldset disabled={submitting}>
                        <input
                            name="city"
                            id="update-user-city-input"
                            type="text"
                            placeholder="City"
                            className={(errors.city ? 'border-red-400 ' : 'border-gray-400 ') + 'form-input px-4 py-3 border rounded-lg w-96'}
                            onChange={handleChange}
                            value={formData.city || ''}
                        />
                        {errors.city && (
                            <div className="line-broken-errors text-red-600">
                                {errors.city}
                            </div>
                        )}

                    </fieldset>
                </div>
                <hr />
                <div className="py-2 flex flex-row justify-between">
                    <label htmlFor="update-user-postcode-input" className="">Postcode</label>
                    <fieldset disabled={submitting}>
                        <input
                            name="postcode"
                            id="update-user-postcode-input"
                            type="text"
                            placeholder="Postcode"
                            className={(errors.postcode ? 'border-red-400 ' : 'border-gray-400 ') + 'form-input px-4 py-3 border rounded-lg w-96'}
                            onChange={handleChange}
                            value={formData.postcode || ''}
                        />
                        {errors.postcode && (
                            <div className="line-broken-errors text-red-600">
                                {errors.postcode}
                            </div>
                        )}

                    </fieldset>
                </div>
                <hr />
                <div className="py-2 flex flex-row justify-end">
                    <div>
                        <button type="reset" className="button border border-gray-400 rounded-lg w-20 shadow-sm cursor-pointer px-2 py-2 text-lg font-medium mx-4">Cancel</button>
                        <button type="submit" className="button border border-gray-400 rounded-lg w-20 shadow-sm cursor-pointer px-2 py-2 bg-orange-600 text-white text-lg font-medium" disabled={empty || submitting}>Save</button>
                    </div>
                </div>
            </form>

        </div>
    );
}