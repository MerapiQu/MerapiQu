export interface INavigationContent {
    title?: string;
    description?: string;
    error?: {
        code: number;
        message: string;
    },
    module?: IModule;
    [key: string]: any;
}
export interface IModule {
    name: string;
    src: string;
    error: {
        code: number;
        message: string;
    }
}
