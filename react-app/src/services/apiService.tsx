import environment from '../../environment.tsx';
import { UnprocessableError } from '../types/unprocessableError.tsx';
export const API_BASE = environment().REACT_APP_API_URL;
export function handleResponse(response: Response): any {
    if (!response.ok) {
        switch (response.status) {
            case 401:
            case 422:
                throw response; // to be processed
            default:
                throw new Error(String(response.status));
        }
    }
    return response.json();
}
export function handleUnprocessableError(data: UnprocessableError): Array<{ name: string, value: string }> {
    const newErrors: { name: string, value: string }[] = [];
    for (const violation of data.violations) {
        let currentString: string = newErrors.reduce((currentString, prop) => {
            return (prop.name === violation.propertyPath) ? prop.value : currentString;
        }, '');
        newErrors.push({ name: violation.propertyPath, value: currentString ? currentString + ' \n ' + violation.title : violation.title });
    }
    return newErrors;
}