import { GoogleGenerativeAI, SchemaType } from "@google/generative-ai";
import dotenv from "dotenv";

// Load biến môi trường từ file .env
dotenv.config();

// Kiểm tra xem đã có API Key chưa
if (!process.env.GEMINI_API_KEY) {
    console.error("❌ LỖI: Chưa tìm thấy GEMINI_API_KEY trong file .env!");
    console.error("Vui lòng lấy API Key tại https://aistudio.google.com/app/apikey và dán vào file .env");
    process.exit(1);
}

// Khởi tạo AI Client
const genAI = new GoogleGenerativeAI(process.env.GEMINI_API_KEY);

// =====================================================================
// BƯỚC 1: ĐỊNH NGHĨA KỸ NĂNG (AGENT SKILL)
// =====================================================================
function getStudentInfo(studentId) {
    console.log(`\n[⚙️ SKILL EXECUTED] Đang truy vấn CSDL ERP cho mã học sinh: ${studentId}...`);
    
    // Giả lập dữ liệu ERP
    const erpDatabase = {
        "SV001": { name: "Nguyễn Văn A", class: "10A1", status: "Đang học", tuition_paid: true },
        "SV002": { name: "Trần Thị B", class: "11B2", status: "Bảo lưu", tuition_paid: false },
    };

    return erpDatabase[studentId] || { error: "Không tìm thấy học sinh với mã này trong ERP" };
}

// Map tên hàm để gọi thực tế
const functions = {
    getStudentInfo: ({ studentId }) => getStudentInfo(studentId)
};

// =====================================================================
// BƯỚC 2: KHAI BÁO TOOL (Giúp Agent hiểu và biết cách dùng Skill)
// =====================================================================
const getStudentInfoTool = {
    functionDeclarations: [
        {
            name: "getStudentInfo",
            description: "Truy xuất thông tin chi tiết của học sinh từ hệ thống ERP dựa vào mã học sinh (studentId).",
            parameters: {
                type: SchemaType.OBJECT,
                properties: {
                    studentId: {
                        type: SchemaType.STRING,
                        description: "Mã học sinh cần tra cứu, ví dụ: SV001, SV002",
                    },
                },
                required: ["studentId"],
            },
        },
    ],
};

// Khởi tạo model với Tool
const model = genAI.getGenerativeModel({
    model: "gemini-2.5-flash",
    tools: [getStudentInfoTool],
});

// =====================================================================
// BƯỚC 3: CHẠY AGENT (VÒNG ĐỜI THỰC THI)
// =====================================================================
async function runAgent() {
    const userPrompt = "Bạn kiểm tra giúp tôi thông tin của học sinh mã SV001 xem bạn ấy đã đóng học phí chưa nhé?";
    console.log(`👤 USER: "${userPrompt}"`);
    console.log("🤖 AGENT: Đang suy nghĩ và lập kế hoạch...");

    try {
        // Mở một session chat
        const chat = model.startChat();
        
        // Gửi yêu cầu của user
        const result = await chat.sendMessage(userPrompt);
        const response = result.response;
        const call = response.functionCalls();

        // Agent quyết định dùng Skill (Routing)
        if (call && call.length > 0) {
            const functionCall = call[0];
            console.log(`\n[🧠 AGENT REASONING] Quyết định dùng skill: "${functionCall.name}" với tham số:`, functionCall.args);
            
            // Thực thi Skill (Acting)
            const skillResult = functions[functionCall.name](functionCall.args);
            console.log(`[📦 SKILL RESULT] Dữ liệu trả về từ ERP:`, skillResult);

            // Trả kết quả lại cho Agent để tổng hợp câu trả lời cuối cùng
            console.log("\n🤖 AGENT: Đang tổng hợp câu trả lời...");
            const finalResult = await chat.sendMessage([{
                functionResponse: {
                    name: functionCall.name,
                    response: skillResult
                }
            }]);

            console.log(`\n💬 AGENT TRẢ LỜI: "${finalResult.response.text()}"`);

        } else {
             // Trường hợp Agent thấy không cần dùng Skill
             console.log(`\n💬 AGENT TRẢ LỜI: "${response.text()}"`);
        }

    } catch (error) {
        console.error("\n❌ LỖI TRONG QUÁ TRÌNH CHẠY:", error.message);
    }
}

// Bắt đầu
runAgent();
