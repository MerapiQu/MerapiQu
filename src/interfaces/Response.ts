export interface Response<T> {
    status: boolean;
    message: string;
    data: T;
}