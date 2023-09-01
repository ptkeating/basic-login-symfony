import RegisterForm from "../form-objects/registerForm"
import { API_BASE, handleResponse } from "./apiService"
export function register(formData: RegisterForm) {
    return fetch(API_BASE + '/api/register',
        { method: 'POST', mode: 'cors', body: JSON.stringify(formData), headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' } }
    ).then(handleResponse);
}