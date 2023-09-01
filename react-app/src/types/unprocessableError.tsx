import { Violation } from "./violation";

export type UnprocessableError = {
    detail: string;
    title: string;
    type: string;
    violations: Violation[]
}