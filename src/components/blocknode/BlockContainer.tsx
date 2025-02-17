import { BlockProps } from "@/interfaces/BlockNode";
import React, { useEffect } from "react";

const BlockContainer = (blockProps: BlockProps) => {

    useEffect(() => {
        console.log(blockProps);
    }, []);

    return (
        <>Block Container</>
    );
}
export default BlockContainer;