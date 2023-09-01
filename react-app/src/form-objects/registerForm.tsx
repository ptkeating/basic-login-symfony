export default class RegisterForm {
    email: string;
    password: string;
    raw_password: string;
    full_name: string;

    constructor() {
        this.email = '';
        this.password = '';
        this.raw_password = '';
        this.full_name = '';
    }
}