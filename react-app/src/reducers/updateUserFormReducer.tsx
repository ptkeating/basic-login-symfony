
import UpdateUserForm from "../form-objects/updateUserForm";

export default function updateUserFormReducer(state: any, event: { name: string, value: string | number, reset?: boolean }): UpdateUserForm {
    if (event.reset) {
        return new UpdateUserForm;
    }
    return {
        ...state,
        [event.name]: event.value
    }
}
export function updateUserFormErrorReducer(state: any, event: { name: string, value: string | number, reset?: boolean }): UpdateUserForm {
    if (event.reset) {
        return new UpdateUserForm;
    }
    return {
        ...state,
        [event.name]: event.value
    }
}