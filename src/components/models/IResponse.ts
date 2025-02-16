export interface IResponse<T> {
    status: boolean;
    message: string;
    data: T;
}