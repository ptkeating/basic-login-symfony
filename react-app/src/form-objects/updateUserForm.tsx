export default class UpdateUserForm {
    id: number;
    full_name: string;
    email: string;
    house_number: string;
    street_address: string;
    city: string;
    postcode: string;

    constructor() {
        this.full_name = '';
        this.email = '';
        this.house_number = '';
        this.street_address = '';
        this.city = '';
        this.postcode = '';
        this.id = 0;
    }
}