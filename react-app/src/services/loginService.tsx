import LoginForm from "../form-objects/loginForm"
import { API_BASE, handleResponse } from "./apiService"
export function login(formData: LoginForm) {
    return fetch(API_BASE + '/api/login_check',
        { method: 'POST', mode: 'cors', body: JSON.stringify({ username: formData.email, password: formData.password }), headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' } }
    ).then(handleResponse);
}