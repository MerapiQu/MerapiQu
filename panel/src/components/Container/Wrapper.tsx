import React, { useEffect, useState } from "react";
import { useApp } from "../../app";
import Client from "../client";
import { BlockProps } from "../Blocks/BlockNode";
import Content from "./Content";

export interface TypeResponse {
  status: boolean;
  message: string;
  data?: {
    scripts: string[];
    styles: string[];
    content: BlockProps[];
    [key: string]: any;
  } | null;
}

const Wrapper = () => {
  const { currentPath } = useApp();
  const [response, setResponse] = useState<TypeResponse | null>({
    status: false,
    message: "",
    data: null,
  });

  useEffect(() => {
    Client.send<TypeResponse>({
      url: currentPath,
      success(body) {
        setResponse(body);
      },
      error(code, message) {
        console.log(code, message);
        setResponse({
          status: false,
          message,
          data: {
            scripts: [],
            styles: [],
            content: [
              {
                tagName: "div",
                classes: [
                  "d-flex",
                  "justify-content-center",
                  "align-items-center",
                ],
                children: [
                  {
                    tagName: "h1",
                    children: `${code} ${message}`,
                  },
                ],
              },
            ],
          },
        });
      },
    });
  }, [currentPath]);

  return (
    <div className="app-wrapper">
      {response?.data?.content && (
        <Content
          styles={response.data.styles}
          scripts={response.data.scripts}
          content={response.data.content}
        />
      )}
    </div>
  );
};
export default Wrapper;
