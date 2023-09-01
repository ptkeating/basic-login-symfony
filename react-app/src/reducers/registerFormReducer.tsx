// import RegisterFormAction from "./RegisterFormAction";

import RegisterForm from "../form-objects/registerForm";

export default function registerFormReducer(state: any, event: { name: string, value: string, reset?: boolean }): RegisterForm {
    if (event.reset) {
        return new RegisterForm;
    }
    return {
        ...state,
        [event.name]: event.value
    }
}
export function registerFormErrorReducer(state: any, event: { name: string, value: string, reset?: boolean }): RegisterForm {
    if (event.reset) {
        return new RegisterForm;
    }
    return {
        ...state,
        [event.name]: event.value
    }
}