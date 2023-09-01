import UpdateUserForm from "../form-objects/updateUserForm";
import { API_BASE, handleResponse } from "./apiService";

export function updateUser(formData: UpdateUserForm) {
    if (!localStorage.getItem('user_id')) {
        throw new Error("User is not logged in");
    }
    return fetch(API_BASE + '/api/users/' + localStorage.getItem('user_id'),
        {
            method: 'PATCH', mode: 'cors', body: JSON.stringify(formData),
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'Authorization': 'Bearer ' + localStorage.getItem('token') }
        })
        .then(handleResponse);
}

export function getUser() {
    if (!localStorage.getItem('user_id')) {
        throw new Error("User is not logged in");
    }
    return fetch(API_BASE + '/api/users/' + localStorage.getItem('user_id'),
        {
            method: 'GET', mode: 'cors',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'Authorization': 'Bearer ' + localStorage.getItem('token') }
        })
        .then(handleResponse);
}