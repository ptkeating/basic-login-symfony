
import LoginForm from "../form-objects/loginForm";

export default function loginFormReducer(state: any, event: { name: string, value: string, reset?: boolean }): LoginForm {
    if (event.reset) {
        return new LoginForm;
    }
    return {
        ...state,
        [event.name]: event.value
    }
}
export function loginFormErrorReducer(state: any, event: { name: string, value: string, reset?: boolean }): LoginForm {
    if (event.reset) {
        return new LoginForm;
    }
    return {
        ...state,
        [event.name]: event.value
    }
}