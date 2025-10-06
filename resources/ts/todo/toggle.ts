import axios from "axios";

let csrfPromise: Promise<void> | null = null;

const ensureCsrfCookie = async () => {
    if (csrfPromise === null) {
        csrfPromise = axios
            .get("/sanctum/csrf-cookie")
            .then(() => {
                /* cookie prepared */
            })
            .catch((error) => {
                csrfPromise = null;
                throw error;
            });
    }

    return csrfPromise;
};

// toggle apiを呼び出す
const toggle = async (id: number, done: boolean) => {
    await ensureCsrfCookie();

    const res = await axios.put(
        "/api/todo/toggle",
        {
            id: id,
            done: done,
        },
        {
            headers: {
                "Content-Type": "application/json; charset=utf-8",
            },
            withCredentials: true,
            withXSRFToken: true,
        }
    );
    return res.status;
};

// checkboxの状態が変わったらtoggle apiを呼び出す
const onCheckChange = async (e: Event) => {
    if (e.type !== "change") return;

    if (!(e.target instanceof HTMLInputElement)) return;

    if (!e.target.hasAttribute("todo-id")) return;

    if (!e.target.classList.contains("todo-done")) return;

    const attrId = e.target.getAttribute("todo-id") ?? "";

    if (!/^[1-9]+\d*$/.test(attrId)) {
        throw Error(`todo-id属性の値が不正です todo-id: ${attrId}`);
    }

    const todoId = Number(attrId);
    const done = e.target.checked;

    const status = await toggle(todoId, done);

    if (status < 200 || status >= 300) {
        alert("更新に失敗しました。通信状況を見てもう一度お試しください");

        // エラー時はチェックを元に戻す
        e.target.checked = !done;

        if (500 <= status) {
            console.error(
                `サーバーエラーが発生しました\nステータス: ${status}`
            );
        }

        return;
    }
};

// windowのloadイベントで初期化
window.addEventListener("DOMContentLoaded", () => {
    // todo-doneクラスを持つ要素にchangeイベントを登録
    document.querySelectorAll(".todo-done").forEach((el) => {
        el.addEventListener("change", onCheckChange);
    });
});
