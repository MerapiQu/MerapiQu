export interface IMenu {
    label: string;
    path: string;
    children?: IMenu[] | IMenuRitch[];
}
export interface IMenuRitch extends IMenu {
    icon: string;
    title: string;
    description: string;
}