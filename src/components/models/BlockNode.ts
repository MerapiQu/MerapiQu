export interface IBlockNode {
    tagName: string;
    attribute: {
        [key: string]: string;
    };
    children: IBlockNode[];
}