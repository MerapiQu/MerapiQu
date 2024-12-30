interface ClientProps<T> {
    url: string;
    method?: string;
    body?: any;
    downloading?: (progress: number, xhr: XMLHttpRequest) => void;
    uploading?: (progress: number, xhr: XMLHttpRequest) => void;
    success: (body: T, xhr: XMLHttpRequest) => void;
    error: (code: number, message: string, xhr: XMLHttpRequest) => void;
}

export default {
    send<T>({ url, method, body, downloading, uploading, success, error }: ClientProps<T>) {
        const xhr = new XMLHttpRequest();
        xhr.open(typeof method == "undefined" ? 'GET' : method, url);

        // Track download progress
        xhr.onprogress = (event) => {
            if (event.lengthComputable && downloading) {
                const progress = (event.loaded / event.total) * 100; // Percentage
                downloading(progress, xhr);
            }
        };

        // Track upload progress
        if (xhr.upload && uploading) {
            xhr.upload.onprogress = (event) => {
                if (event.lengthComputable) {
                    const progress = (event.loaded / event.total) * 100; // Percentage
                    uploading(progress, xhr);
                }
            };
        }

        // Handle response
        xhr.onreadystatechange = () => {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status >= 200 && xhr.status < 300) {
                    try {
                        const contentType = xhr.getResponseHeader('content-type');
                        if (contentType && contentType.includes('application/json')) {
                            success(JSON.parse(xhr.response), xhr)
                        } else success(xhr.response, xhr);
                    } catch (e) {
                        error(xhr.status, e.message || e, xhr)
                    }
                } else {
                    error(xhr.status, xhr.statusText, xhr);
                }
            }
        };

        // Handle network error
        xhr.onerror = () => {
            error(xhr.status, xhr.statusText || "Network error", xhr);
        };

        // Send the request
        xhr.send(body);
    }
};
